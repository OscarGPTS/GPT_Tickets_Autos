<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreated extends Notification implements ShouldQueue
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
        $encargadosEmails = explode(',', config('mail.encargados_emails', ''));
        $ccEmails = explode(',', config('mail.cc_emails', ''));

        return (new MailMessage)
            ->subject('Nueva Solicitud de Vehículo - GPT Services')
            ->greeting('¡Hola!')
            ->line('Se ha recibido una nueva solicitud de vehículo.')
            ->line('**Solicitante:** ' . $this->ticket->user->name)
            ->line('**Destino:** ' . $this->ticket->destination)
            ->line('**Fecha Solicitada:** ' . $this->ticket->requested_date->format('d/m/Y'))
            ->line('**Hora:** ' . $this->ticket->requested_time_start)
            ->line('**Motivo:** ' . $this->ticket->purpose)
            ->action('Ver Solicitud', route('tickets.show', $this->ticket))
            ->line('Por favor, revisa y procesa esta solicitud lo antes posible.')
            ->cc($ccEmails);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'title' => 'Nueva solicitud de vehículo',
            'message' => 'Solicitud de ' . $this->ticket->user->name . ' para ' . $this->ticket->destination,
            'action_url' => route('tickets.show', $this->ticket),
        ];
    }
}
