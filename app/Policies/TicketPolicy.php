<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine if the user can view any tickets.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos pueden ver tickets (filtrados por rol en el controlador)
    }

    /**
     * Determine if the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // El solicitante, el despachador, el aprobador y los encargados pueden ver
        return $user->id === $ticket->user_id
            || $user->id === $ticket->dispatcher_id
            || $user->id === $ticket->approved_by
            || $user->isEncargado();
    }

    /**
     * Determine if the user can create tickets.
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine if the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Solo el solicitante puede editar su ticket si está pendiente
        return $user->id === $ticket->user_id && $ticket->canBeEdited();
    }

    /**
     * Determine if the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Solo encargados pueden eliminar tickets
        return $user->isEncargado();
    }

    /**
     * Determine if the user can approve/reject the ticket.
     */
    public function approve(User $user, Ticket $ticket): bool
    {
        return $user->isEncargado();
    }

    /**
     * Determine if the user can perform checkout.
     */
    public function checkout(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->dispatcher_id || $user->isEncargado();
    }

    /**
     * Determine if the user can perform checkin.
     */
    public function checkin(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->dispatcher_id || $user->isEncargado();
    }

    /**
     * Determine if the user can rate the ticket.
     */
    public function rate(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id && $ticket->canBeRated();
    }
}
