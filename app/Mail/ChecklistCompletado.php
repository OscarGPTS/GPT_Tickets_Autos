<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChecklistCompletado extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $checklist;
    public $tipo;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, $checklist, string $tipo)
    {
        $this->ticket = $ticket;
        $this->checklist = $checklist;
        $this->tipo = $tipo; // 'salida' o 'entrada'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->tipo === 'salida' 
            ? 'Check-Out Realizado - ' . $this->ticket->requisicion
            : 'Check-In Realizado - ' . $this->ticket->requisicion;
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.checklist-completado',
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
