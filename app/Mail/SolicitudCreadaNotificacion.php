<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Services\RHService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SolicitudCreadaNotificacion extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ticket;
    public $ccEmails = [];
    public $encargadosEmails = [];

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
        
        // Obtener emails de encargados configurados
        $this->encargadosEmails = array_filter(
            explode(',', config('mail.encargados_emails', '')),
            fn($email) => trim($email) !== ''
        );

        // Obtener emails de CC configurados
        $ccEmails = array_filter(
            explode(',', config('mail.cc_emails', '')),
            fn($email) => trim($email) !== ''
        );
        
        // Obtener email del jefe directo desde API
        try {
            $rhService = new RHService();
            $jefeDirectoEmail = $rhService->obtenerEmailJefeDirecto($ticket->user->email);
            
            if ($jefeDirectoEmail) {
                $ccEmails[] = trim($jefeDirectoEmail);
                Log::info("Jefe directo obtenido para ticket #{$ticket->id}", [
                    'user_email' => $ticket->user->email,
                    'jefe_email' => $jefeDirectoEmail
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("No se pudo obtener jefe directo para ticket #{$ticket->id}", [
                'error' => $e->getMessage()
            ]);
        }

        // Eliminar duplicados
        $this->ccEmails = array_unique($ccEmails);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Solicitud de Vehículo - Folio: ' . ($this->ticket->folio ?? 'PENDIENTE'),
            to: $this->encargadosEmails,
            cc: $this->ccEmails,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-creada',
            with: [
                'ticket' => $this->ticket,
                'solicitante' => $this->ticket->user->name,
                'destino' => $this->ticket->destination,
                'fecha' => $this->ticket->requested_date->format('d/m/Y'),
                'hora' => $this->ticket->requested_time_start,
                'motivo' => $this->ticket->purpose,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
