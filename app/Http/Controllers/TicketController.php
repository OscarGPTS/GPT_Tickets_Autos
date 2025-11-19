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
use Illuminate\View\View;

class TicketController extends Controller
{
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
        return view('tickets.create');
    }

    /**
     * Guardar nueva requisición
     */
    public function store(Request $request): RedirectResponse
    {
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

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            ...$validated,
        ]);

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
