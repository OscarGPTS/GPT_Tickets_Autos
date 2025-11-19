<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRejected extends Notification implements ShouldQueue
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
            ->subject('Solicitud Rechazada - GPT Services')
            ->greeting('Hola ' . $notifiable->name)
            ->line('Lamentamos informarte que tu solicitud de vehículo ha sido **RECHAZADA**.')
            ->line('**Destino:** ' . $this->ticket->destination)
            ->line('**Fecha Solicitada:** ' . $this->ticket->requested_date->format('d/m/Y'))
            ->line('**Motivo del Rechazo:**')
            ->line($this->ticket->rejection_reason)
            ->action('Ver Detalles', route('tickets.show', $this->ticket))
            ->line('Si tienes dudas, por favor contacta al área de administración.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Solicitud Rechazada',
            'message' => 'Tu solicitud para ' . $this->ticket->destination . ' ha sido rechazada',
            'action_url' => route('tickets.show', $this->ticket),
        ];
    }
}
