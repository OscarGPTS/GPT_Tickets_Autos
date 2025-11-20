<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\TicketCreated;
use App\Notifications\TicketApproved;
use App\Notifications\TicketRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Listar tickets
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $query = Ticket::with(['user', 'vehicle', 'dispatcher']);

        // Filtrar según rol
        if ($user->isUsuario()) {
            $query->forUser($user->id);
        } elseif ($user->isDespachador()) {
            $query->forDispatcher($user->id);
        }

        // Aplicar filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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
        $vehicles = \App\Models\Vehicle::where('status', 'disponible')->get();
        return view('tickets.create', compact('vehicles'));
    }

    /**
     * Guardar nueva requisición
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'destination' => 'nullable|string|max:255',
            'purpose' => 'nullable|string',
            'requested_date' => 'nullable|date',
            'requested_time_start' => 'nullable',
            'requested_time_end' => 'nullable',
            'conductor_name' => 'nullable|string|max:255',
            'conductor_phone' => 'nullable|string|max:20',
            
            // Campos del checklist
            'vehicle_id' => 'required|exists:vehicles,id',
            'folio' => 'nullable|string|max:50',
            'destino' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'marca' => 'required|string|max:255',
            'hora_salida' => 'required',
            'hora_entrada' => 'nullable',
            'fecha' => 'required|date',
            'kilometraje_inicial' => 'required|numeric',
            'nivel_combustible_inicial' => 'required|string',
            'placas' => 'required|string|max:20',
            
            // Campos booleanos del checklist (radio buttons)
            'llanta_delantera_derecha' => 'nullable|boolean',
            'llanta_delantera_izquierda' => 'nullable|boolean',
            'llanta_trasera_derecha' => 'nullable|boolean',
            'llanta_trasera_izquierda' => 'nullable|boolean',
            'llanta_refaccion' => 'nullable|boolean',
            'llanta_vida_delantera' => 'nullable|boolean',
            'llanta_vida_trasera' => 'nullable|boolean',
            'presion_adecuada' => 'nullable|boolean',
            'parabrisas' => 'nullable|boolean',
            'cofre' => 'nullable|boolean',
            'intermitentes' => 'nullable|boolean',
            'salpicadera' => 'nullable|boolean',
            'parrilla' => 'nullable|boolean',
            'defensa' => 'nullable|boolean',
            'molduras' => 'nullable|boolean',
            'placa' => 'nullable|boolean',
            'antena' => 'nullable|boolean',
            'mata_chispas' => 'nullable|boolean',
            'alarma' => 'nullable|boolean',
            'extintor' => 'nullable|boolean',
            'botiquin' => 'nullable|boolean',
            'tarjeta_circulacion' => 'nullable|boolean',
            'licencia_vigente' => 'nullable|boolean',
            'poliza_seguro' => 'nullable|boolean',
            'triangulo_emergencia' => 'nullable|boolean',
            'direccional_derecha' => 'nullable|boolean',
            'direccional_izquierda' => 'nullable|boolean',
            'luz_stop' => 'nullable|boolean',
            'faros' => 'nullable|boolean',
            'luces_altas' => 'nullable|boolean',
            'luz_interior' => 'nullable|boolean',
            'calaveras' => 'nullable|boolean',
            'tablero_indicadores' => 'nullable|boolean',
            'switch_encendido' => 'nullable|boolean',
            'controles_ac' => 'nullable|boolean',
            'defroster' => 'nullable|boolean',
            'radio' => 'nullable|boolean',
            'volante' => 'nullable|boolean',
            'bolsa_aire' => 'nullable|boolean',
            'espejo_interior' => 'nullable|boolean',
            'freno_mano' => 'nullable|boolean',
            'encendedor' => 'nullable|boolean',
            'guantera' => 'nullable|boolean',
            'manijas_interiores' => 'nullable|boolean',
            'seguros' => 'nullable|boolean',
            'asientos' => 'nullable|boolean',
            'cinturon_seguridad' => 'nullable|boolean',
            'tapetes' => 'nullable|boolean',
            'coderas' => 'nullable|boolean',
            'nivel_aceite_motor' => 'nullable|boolean',
            'nivel_anticongelante' => 'nullable|boolean',
            'nivel_liquido_frenos' => 'nullable|boolean',
            'bateria' => 'nullable|boolean',
            'bayoneta_aceite' => 'nullable|boolean',
            'tapones' => 'nullable|boolean',
            'bocina_claxon' => 'nullable|boolean',
            'radiador' => 'nullable|boolean',
            'gato' => 'nullable|boolean',
            'llave_ruedas' => 'nullable|boolean',
            'cables_pasa_corrientes' => 'nullable|boolean',
            'caja_herramientas' => 'nullable|boolean',
            'dado_birlo_seguridad' => 'nullable|boolean',
            'calcomanias_permisos' => 'nullable|boolean',
            'calcomanias_velocidad' => 'nullable|boolean',
            'facia' => 'nullable|boolean',
            
            // Campos de texto del checklist
            'mantenimiento_preventivo' => 'nullable|string',
            'mantenimiento_correctivo' => 'nullable|string',
            'condicion_carroceria' => 'nullable|string',
            'condicion_carroceria_imagen' => 'nullable|string', // Base64 image data
            'tipo_vehiculo' => 'nullable|string|in:auto,camioneta',
            'responsable_recibo_uso' => 'required|string|max:255',
            'responsable_entrega' => 'required|string|max:255',
        ]);

        // Crear el ticket primero (con campos básicos)
        $ticketData = [
            'user_id' => Auth::id(),
            'vehicle_id' => $validated['vehicle_id'],
            'destination' => $validated['destino'] ?? $validated['destination'] ?? null,
            'purpose' => $validated['purpose'] ?? 'Solicitud de vehículo',
            'requested_date' => $validated['fecha'] ?? $validated['requested_date'] ?? now(),
            'requested_time_start' => $validated['hora_salida'] ?? $validated['requested_time_start'] ?? null,
            'requested_time_end' => $validated['hora_entrada'] ?? $validated['requested_time_end'] ?? null,
            'conductor_name' => $validated['conductor_name'] ?? null,
            'conductor_phone' => $validated['conductor_phone'] ?? null,
        ];

        $ticket = Ticket::create($ticketData);

        // Crear el checklist asociado con todos los campos
        $checklistData = [
            'ticket_id' => $ticket->id,
            'folio' => $validated['folio'] ?? $ticket->id, // Usar el ID del ticket como folio si no se proporciona
            'fecha' => $validated['fecha'],
            'destino' => $validated['destino'],
            'modelo' => $validated['modelo'],
            'marca' => $validated['marca'],
            'placas' => $validated['placas'],
            'hora_salida' => $validated['hora_salida'],
            'hora_entrada' => $validated['hora_entrada'] ?? null,
            'kilometraje_inicial' => $validated['kilometraje_inicial'],
            'nivel_combustible_inicial' => $validated['nivel_combustible_inicial'],
            'tipo_inspeccion' => 'salida', // Checkout al crear el ticket
            
            // Llantas
            'llanta_delantera_derecha' => $validated['llanta_delantera_derecha'] ?? false,
            'llanta_delantera_izquierda' => $validated['llanta_delantera_izquierda'] ?? false,
            'llanta_trasera_derecha' => $validated['llanta_trasera_derecha'] ?? false,
            'llanta_trasera_izquierda' => $validated['llanta_trasera_izquierda'] ?? false,
            'llanta_refaccion' => $validated['llanta_refaccion'] ?? false,
            'llanta_vida_delantera' => $validated['llanta_vida_delantera'] ?? false,
            'llanta_vida_trasera' => $validated['llanta_vida_trasera'] ?? false,
            'presion_adecuada' => $validated['presion_adecuada'] ?? false,
            
            // Frontal
            'parabrisas' => $validated['parabrisas'] ?? false,
            'cofre' => $validated['cofre'] ?? false,
            'intermitentes' => $validated['intermitentes'] ?? false,
            'salpicadera' => $validated['salpicadera'] ?? false,
            'parrilla' => $validated['parrilla'] ?? false,
            'defensa' => $validated['defensa'] ?? false,
            'molduras' => $validated['molduras'] ?? false,
            'placa' => $validated['placa'] ?? false,
            'antena' => $validated['antena'] ?? false,
            'facia' => $validated['facia'] ?? false,
            
            // Otros
            'mata_chispas' => $validated['mata_chispas'] ?? false,
            'alarma' => $validated['alarma'] ?? false,
            'extintor' => $validated['extintor'] ?? false,
            'botiquin' => $validated['botiquin'] ?? false,
            'tarjeta_circulacion' => $validated['tarjeta_circulacion'] ?? false,
            'licencia_vigente' => $validated['licencia_vigente'] ?? false,
            'poliza_seguro' => $validated['poliza_seguro'] ?? false,
            'triangulo_emergencia' => $validated['triangulo_emergencia'] ?? false,
            
            // Luces
            'direccional_derecha' => $validated['direccional_derecha'] ?? false,
            'direccional_izquierda' => $validated['direccional_izquierda'] ?? false,
            'luz_stop' => $validated['luz_stop'] ?? false,
            'faros' => $validated['faros'] ?? false,
            'luces_altas' => $validated['luces_altas'] ?? false,
            'luz_interior' => $validated['luz_interior'] ?? false,
            'calaveras' => $validated['calaveras'] ?? false,
            
            // Interior
            'tablero_indicadores' => $validated['tablero_indicadores'] ?? false,
            'switch_encendido' => $validated['switch_encendido'] ?? false,
            'controles_ac' => $validated['controles_ac'] ?? false,
            'defroster' => $validated['defroster'] ?? false,
            'radio' => $validated['radio'] ?? false,
            'volante' => $validated['volante'] ?? false,
            'bolsa_aire' => $validated['bolsa_aire'] ?? false,
            'espejo_interior' => $validated['espejo_interior'] ?? false,
            'freno_mano' => $validated['freno_mano'] ?? false,
            'encendedor' => $validated['encendedor'] ?? false,
            'guantera' => $validated['guantera'] ?? false,
            'manijas_interiores' => $validated['manijas_interiores'] ?? false,
            'seguros' => $validated['seguros'] ?? false,
            'asientos' => $validated['asientos'] ?? false,
            'cinturon_seguridad' => $validated['cinturon_seguridad'] ?? false,
            'tapetes' => $validated['tapetes'] ?? false,
            'coderas' => $validated['coderas'] ?? false,
            
            // Motor
            'nivel_aceite_motor' => $validated['nivel_aceite_motor'] ?? false,
            'nivel_anticongelante' => $validated['nivel_anticongelante'] ?? false,
            'nivel_liquido_frenos' => $validated['nivel_liquido_frenos'] ?? false,
            'bateria' => $validated['bateria'] ?? false,
            'bayoneta_aceite' => $validated['bayoneta_aceite'] ?? false,
            'tapones' => $validated['tapones'] ?? false,
            'bocina_claxon' => $validated['bocina_claxon'] ?? false,
            'radiador' => $validated['radiador'] ?? false,
            
            // Herramientas
            'gato' => $validated['gato'] ?? false,
            'llave_ruedas' => $validated['llave_ruedas'] ?? false,
            'cables_pasa_corrientes' => $validated['cables_pasa_corrientes'] ?? false,
            'caja_herramientas' => $validated['caja_herramientas'] ?? false,
            'dado_birlo_seguridad' => $validated['dado_birlo_seguridad'] ?? false,
            
            // Calcomanías
            'calcomanias_permisos' => $validated['calcomanias_permisos'] ?? false,
            'calcomanias_velocidad' => $validated['calcomanias_velocidad'] ?? false,
            
            // Mantenimiento y condición
            'mantenimiento_preventivo' => $validated['mantenimiento_preventivo'] ?? null,
            'mantenimiento_correctivo' => $validated['mantenimiento_correctivo'] ?? null,
            
            // Guardar condición de carrocería con imagen si existe
            'condicion_carroceria_log' => json_encode([
                'checkout' => [
                    'descripcion' => $validated['condicion_carroceria'] ?? null,
                    'imagen' => $this->saveVehicleImage($request->input('condicion_carroceria_imagen'), $ticket->id),
                    'fecha' => now()->toDateTimeString(),
                ]
            ]),
            
            // Responsables
            'responsable_recibo_uso' => $validated['responsable_recibo_uso'],
            'responsable_entrega' => $validated['responsable_entrega'],
        ];

        $ticket->checklists()->create($checklistData);

        // Actualizar el kilometraje del vehículo con el kilometraje inicial registrado
        if (isset($validated['vehicle_id']) && isset($validated['kilometraje_inicial'])) {
            $vehicle = \App\Models\Vehicle::find($validated['vehicle_id']);
            if ($vehicle) {
                $vehicle->update([
                    'current_mileage' => $validated['kilometraje_inicial']
                ]);
            }
        }

        // Notificar a los encargados
        $this->notifyEncargados($ticket);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Requisición creada exitosamente. Se ha notificado a los encargados.');
    }

    /**
     * Mostrar detalle del ticket
     */
    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);
        
        $ticket->load([
            'user',
            'vehicle',
            'dispatcher',
            'approver',
            'conductorLicense',
            'checkoutChecklist',
            'checkinChecklist'
        ]);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

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
        $this->authorize('update', $ticket);

        if (!$ticket->canBeEdited()) {
            return back()->with('error', 'Este ticket ya no puede ser editado.');
        }

        $validated = $request->validate([
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string',
            'requested_date' => 'required|date|after_or_equal:today',
            'requested_time_start' => 'required',
            'requested_time_end' => 'nullable',
            'passenger_count' => 'required|integer|min:1',
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
        $this->authorize('approve', $ticket);

        if (!$ticket->canBeApproved()) {
            return back()->with('error', 'Este ticket no puede ser aprobado.');
        }

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'dispatcher_id' => 'required|exists:users,id',
            'conductor_name' => 'nullable|string|max:255',
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
            'conductor_name' => $validated['conductor_name'] ?? $ticket->user->name,
            'approved_by' => Auth::id(),
            'status' => 'aprobado',
            'approved_at' => now(),
        ]);

        // Actualizar estado del vehículo
        $vehicle->update(['status' => 'en_uso']);

        // Notificar al solicitante y despachador
        $ticket->user->notify(new TicketApproved($ticket));
        $ticket->dispatcher->notify(new \App\Notifications\VehicleAssigned($ticket));

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket aprobado exitosamente. Folio: {$folio}");
    }

    /**
     * Rechazar ticket
     */
    public function reject(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('approve', $ticket);

        if (!$ticket->canBeApproved()) {
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

        // Notificar al solicitante
        $ticket->user->notify(new TicketRejected($ticket));

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket rechazado exitosamente.');
    }

    /**
     * Calificar servicio
     */
    public function rate(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('rate', $ticket);

        if (!$ticket->canBeRated()) {
            return back()->with('error', 'Este ticket no puede ser calificado aún.');
        }

        $validated = $request->validate([
            'service_rating' => 'required|integer|min:1|max:5',
            'vehicle_rating' => 'required|integer|min:1|max:5',
            'rating_comments' => 'nullable|string',
        ]);

        $ticket->update([
            ...$validated,
            'status' => 'completado',
            'completed_at' => now(),
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Gracias por tu calificación.');
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
     * Notificar a los encargados sobre nueva solicitud
     */
    private function notifyEncargados(Ticket $ticket): void
    {
        $encargados = User::whereHas('roles', function ($query) {
            $query->where('name', 'encargado');
        })->get();

        Notification::send($encargados, new TicketCreated($ticket));

        // También notificar al jefe inmediato si existe
        if ($ticket->user->immediateBoss) {
            $ticket->user->immediateBoss->notify(new TicketCreated($ticket));
        }
    }
}
