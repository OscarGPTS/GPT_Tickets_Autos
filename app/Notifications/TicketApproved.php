<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Ticket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Solicitud Aprobada - Folio: ' . $this->ticket->folio)
            ->greeting('¡Buenas noticias!')
            ->line('Tu solicitud de vehículo ha sido **APROBADA**.')
            ->line('**Folio:** ' . $this->ticket->folio)
            ->line('**Vehículo Asignado:** ' . $this->ticket->vehicle->full_name)
            ->line('**Despachador:** ' . $this->ticket->dispatcher->name)
            ->line('**Destino:** ' . $this->ticket->destination)
            ->line('**Fecha:** ' . $this->ticket->requested_date->format('d/m/Y'))
            ->action('Ver Detalles', route('tickets.show', $this->ticket))
            ->line('El despachador realizará el checkout del vehículo próximamente.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Solicitud Aprobada',
            'message' => 'Tu solicitud con folio ' . $this->ticket->folio . ' ha sido aprobada',
            'action_url' => route('tickets.show', $this->ticket),
        ];
    }
}
