<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use App\Mail\SolicitudCreada;
use App\Mail\SolicitudRechazada;
use App\Mail\DespachadorAsignado;
use App\Notifications\TicketCreated;
use App\Notifications\TicketApproved;
use App\Notifications\TicketRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Listar tickets (historial del usuario)
     * 
     * Para usuarios: muestra TODOS los tickets donde es solicitante O conductor (sin filtros de status)
     * Para despachadores: muestra todos los tickets asignados
     * Para encargados: muestra tickets pendientes por defecto o todos si se solicita
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $query = Ticket::query();
        
        // Filtrar según rol
        if ($user->isUsuario()) {
            // Mostrar tickets donde el usuario es solicitante
            $query->where('user_id', $user->id);
        } elseif ($user->isDespachador()) {
            $query->forDispatcher($user->id);
            
            // Aplicar filtros de status para despachador
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
        } elseif ($user->isEncargado()) {
            // Para encargado: mostrar solo pendientes por defecto, a menos que pida "todas"
            if (!$request->has('view') || $request->view !== 'todas') {
                $query->where('status', 'pendiente');
            }
        }

        // Aplicar filtros de fechas (para todos)
        if ($request->filled('date_from')) {
            $query->whereDate('requested_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('requested_date', '<=', $request->date_to);
        }

        $tickets = $query->latest()->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): View
    {
        $vehicles = Vehicle::where('status', 'disponible')->get();
        return view('tickets.create', compact('vehicles'));
    }

    /**
     * Guardar nueva requisición (formulario simple)
     */
    public function store(Request $request): RedirectResponse
    {
        
        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'cliente' => 'nullable|string|max:255',
            'requested_date' => 'required|date',
            'requested_time_start' => 'required',
            'requested_time_end' => 'nullable',
            'purpose' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'folio' => Ticket::generateFolio(),
            'requisicion' => "",
            'user_id' => Auth::id(),
            'destination' => $validated['destination'],
            'cliente' => $validated['cliente'] ?? null,
            'purpose' => $validated['purpose'],
            'requested_date' => $validated['requested_date'],
            'requested_time_start' => $validated['requested_time_start'],
            'requested_time_end' => $validated['requested_time_end'] ?? null,
            'status' => 'pendiente',
        ]);

        $this->notifyEncargados($ticket);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Requisición enviada exitosamente. Los encargados la revisarán pronto.');
    }

    /**
     * Mostrar detalle del ticket
     */
    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'user',
            'vehicle',
            'dispatcher',
            'approver',
            'conductorLicense',
            'checkoutChecklist',
            'checkinChecklist'
        ]);

        $dispatchers_id = DB::table('role_user')->where('role_id', 2)->pluck('user_id');

        $dispatchers = User::whereIn('id', $dispatchers_id)->get();

        return view('tickets.show', compact('ticket', 'dispatchers'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Ticket $ticket): View
    {
        if (!$ticket->canBeEdited()) {
            abort(403, 'Este ticket ya no puede ser editado.');
        }

        return view('tickets.edit', compact('ticket'));
    }

    /**
     * Actualizar ticket
     */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!$ticket->canBeEdited()) {
            return back()->with('error', 'Este ticket ya no puede ser editado.');
        }

        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string',
            'requested_date' => 'required|date|after_or_equal:today',
            'requested_time_start' => 'required',
            'requested_time_end' => 'nullable',
            'conductor_name' => 'nullable|string|max:255',
            'conductor_phone' => 'nullable|string|max:20',
            'additional_notes' => 'nullable|string',
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Requisición actualizada exitosamente.');
    }

    /**
     * Aprobar ticket y asignar recursos
     */
    public function approve(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!$ticket->canBeApproved()) {
            return back()->with('error', 'Este ticket no puede ser aprobado.');
        }

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'dispatcher_id' => 'required|exists:users,id',
        ]);

        // Verificar disponibilidad del vehículo
        $vehicle = Vehicle::find($validated['vehicle_id']);
        if (!$vehicle->isAvailable()) {
            return back()->with('error', 'El vehículo seleccionado no está disponible.');
        }

        // Generar folio
        $folio = Ticket::generateFolio();

        $ticket->update([
            'folio' => $folio,
            'vehicle_id' => $validated['vehicle_id'],
            'dispatcher_id' => $validated['dispatcher_id'],
            // Mantener conductor como solicitante por defecto
            'conductor_name' => $ticket->user->name,
            'approved_by' => Auth::id(),
            'status' => 'aprobado',
            'approved_at' => now(),
        ]);

        // Actualizar estado del vehículo
        $vehicle->update(['status' => 'en_uso']);

        // Notificar al solicitante
        $ticket->user->notify(new TicketApproved($ticket));

        // Enviar correo al despachador; si falla el SMTP solo registramos el error
        try {
            Mail::to($ticket->dispatcher->email)->send(new DespachadorAsignado($ticket));
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar correo al despachador', [
                'ticket_id' => $ticket->id,
                'dispatcher_id' => $ticket->dispatcher_id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket aprobado exitosamente. Folio: {$folio}. Se ha notificado al despachador.");
    }

    /**
     * Rechazar ticket
     */
    public function reject(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!$ticket->canBeRejected()) {
            return back()->with('error', 'Este ticket no puede ser rechazado.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $ticket->update([
            'status' => 'rechazado',
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),
        ]);

        // Enviar correo de forma asincrónica
        Mail::to($ticket->user->email)->queue(new SolicitudRechazada($ticket));

        return redirect()->route('dashboard')
            ->with('success', 'Ticket rechazado exitosamente.');
    }

    /**
     * Asignar despachador a ticket aprobado (solo encargados)
     */
    public function assignDispatcher(Request $request, Ticket $ticket): RedirectResponse
    {
        // Verificar que el usuario sea encargado o admin
        $user = Auth::user();
        if (!$user->hasRole('encargado') && !$user->hasRole('admin')) {
            return back()->with('error', 'No tienes permisos para asignar despachadores.');
        }

        // Verificar que el ticket esté aprobado
        if ($ticket->status !== 'aprobado') {
            return back()->with('error', 'Solo se pueden asignar despachadores a tickets aprobados.');
        }

        $validated = $request->validate([
            'dispatcher_id' => 'required|exists:users,id',
        ]);

        // Verificar que el usuario seleccionado sea despachador
        $dispatcher = User::findOrFail($validated['dispatcher_id']);
        if (!$dispatcher->hasRole('dispatcher')) {
            return back()->with('error', 'El usuario seleccionado no es un despachador válido.');
        }

        // Asignar despachador
        $ticket->update([
            'dispatcher_id' => $validated['dispatcher_id'],
        ]);

        // Enviar correo al despachador notificando la asignación
        try {
            Mail::to($dispatcher->email)->send(new DespachadorAsignado($ticket->fresh()));
        } catch (\Exception $e) {
            Log::warning('No se pudo enviar correo al despachador: ' . $e->getMessage());
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Despachador asignado exitosamente. Se ha enviado una notificación por correo.');
    }

    /**
     * Calificar servicio
     */
    public function rate(Request $request, Ticket $ticket): RedirectResponse
    {
        if (!$ticket->canBeRated()) {
            return back()->with('error', 'Este ticket no puede ser calificado aún.');
        }

        $user = $request->user();
        $isAllowedRater = $user->id === $ticket->user_id
            || $user->isDespachador()
            || $user->isEncargado()
            || $user->hasRole('admin');

        if (!$isAllowedRater) {
            return back()->with('error', 'No tienes permiso para calificar este servicio.');
        }

        $validated = $request->validate([
            'service_rating' => 'required|integer|min:1|max:5',
            'rating_comments' => 'nullable|string|max:255',
        ]);

        $ticket->update([
            'service_rating' => $validated['service_rating'],
            'rating_comments' => $validated['rating_comments'] ?? null,
        ]);

        // Notificar a Ana Lilia y José sobre la calificación
        $this->notifyEncargadosSatisfaccion($ticket);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', '¡Gracias por tu calificación! Tu opinión nos ayuda a mejorar.');
    }

    /**
     * Guardar imagen del vehículo con daños marcados
     */
    private function saveVehicleImage(?string $base64Image, int $ticketId): ?string
    {
        if (empty($base64Image)) {
            return null;
        }

        try {
            // Extraer el contenido base64 (eliminar el prefijo data:image/png;base64,)
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
            } else {
                return null;
            }

            // Decodificar la imagen
            $imageData = base64_decode($base64Image);
            
            if ($imageData === false) {
                return null;
            }

            // Crear nombre único para la imagen
            $fileName = 'vehicle_damage_' . $ticketId . '_' . time() . '.' . $type;
            $filePath = 'vehicle_damages/' . $fileName;

            // Guardar la imagen en el storage público
            Storage::disk('public')->put($filePath, $imageData);

            // Retornar la ruta relativa
            return $filePath;
        } catch (\Exception $e) {
            Log::error('Error al guardar imagen del vehículo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Notificar a los encargados sobre encuesta de satisfacción
     */
    private function notifyEncargadosSatisfaccion(Ticket $ticket): void
    {
        // Obtener los emails de los encargados desde config
        $toEmails = array_filter(
            array_map('trim', explode(',', config('mail.encargados_emails', ''))),
            fn($email) => !empty($email)
        );

        // Enviar notificación de calificación
        if (!empty($toEmails)) {
            try {
                foreach ($toEmails as $email) {
                    \Mail::to($email)->send(
                        new \App\Mail\EncuestaSatisfaccion($ticket)
                    );
                }
            } catch (\Exception $e) {
                \Log::warning('Error al enviar encuesta de satisfacción: ' . $e->getMessage());
            }
        }
    }

    /**
     * Notificar a los encargados sobre nueva solicitud
     */
    private function notifyEncargados(Ticket $ticket): void
    {
        // Obtener los emails de los encargados desde config (destinatarios principales)
        $toEmails = array_filter(
            array_map('trim', explode(',', config('mail.encargados_emails', ''))),
            fn($email) => !empty($email)
        );

        // Obtener los emails de copia
        $ccEmails = array_filter(
            array_map('trim', explode(',', config('mail.cc_emails', ''))),
            fn($email) => !empty($email)
        );

        // Agregar email del usuario solicitante al CC
        $ccEmails[] = $ticket->user->email;

        // Obtener email del jefe directo del solicitante desde la API
        try {
            $rhService = new \App\Services\RHService();
            $jefeDirectoEmail = $rhService->obtenerEmailJefeDirecto($ticket->user->email);
            
            if ($jefeDirectoEmail && !in_array(trim($jefeDirectoEmail), $ccEmails)) {
                $ccEmails[] = trim($jefeDirectoEmail);
            }
        } catch (\Exception $e) {
            \Log::warning('No se pudo obtener el jefe directo: ' . $e->getMessage());
        }

        // Eliminar duplicados
        $ccEmails = array_unique($ccEmails);

        // Enviar un único correo a los encargados con todos en CC
        if (!empty($toEmails)) {
            try {
                \Mail::send(
                    new \App\Mail\SolicitudCreada($ticket, $toEmails, $ccEmails)
                );
            } catch (\Exception $e) {
                \Log::error('Error al enviar correo de solicitud: ' . $e->getMessage());
            }
        }
    }
}
