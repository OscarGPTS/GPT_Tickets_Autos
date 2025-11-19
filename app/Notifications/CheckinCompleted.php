<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CheckinCompleted extends Notification implements ShouldQueue
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
            ->subject('Servicio Finalizado - Folio: ' . $this->ticket->folio)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('El servicio de vehículo ha sido finalizado exitosamente.')
            ->line('**Folio:** ' . $this->ticket->folio)
            ->line('**Vehículo:** ' . $this->ticket->vehicle->full_name)
            ->line('**Destino:** ' . $this->ticket->destination)
            ->line('Nos gustaría conocer tu opinión sobre el servicio.')
            ->action('Calificar Servicio', route('tickets.rate', $this->ticket))
            ->line('Tu retroalimentación nos ayuda a mejorar.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Servicio Finalizado',
            'message' => 'Folio ' . $this->ticket->folio . ' - Califica el servicio',
            'action_url' => route('tickets.rate', $this->ticket),
        ];
    }
}
