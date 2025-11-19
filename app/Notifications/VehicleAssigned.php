<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleAssigned extends Notification implements ShouldQueue
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
            ->subject('Vehículo Asignado - Folio: ' . $this->ticket->folio)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se te ha asignado un vehículo para checkout.')
            ->line('**Folio:** ' . $this->ticket->folio)
            ->line('**Vehículo:** ' . $this->ticket->vehicle->full_name)
            ->line('**Solicitante:** ' . $this->ticket->user->name)
            ->line('**Destino:** ' . $this->ticket->destination)
            ->line('**Fecha:** ' . $this->ticket->requested_date->format('d/m/Y'))
            ->line('**Hora:** ' . $this->ticket->requested_time_start)
            ->action('Realizar Checkout', route('checklists.checkout', $this->ticket))
            ->line('Por favor, realiza el checkout del vehículo a tiempo.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Vehículo Asignado',
            'message' => 'Folio ' . $this->ticket->folio . ' - Realizar checkout',
            'action_url' => route('checklists.checkout', $this->ticket),
        ];
    }
}
