<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $evento;
    public $mensaje;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, $evento, $mensaje = '')
    {
        $this->ticket = $ticket;
        $this->evento = $evento;
        $this->mensaje = $mensaje;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[TK-" . str_pad($this->ticket->id, 4, '0', STR_PAD_LEFT) . "] " . $this->evento,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket_notification',
            with: [
                'ticket' => $this->ticket,
                'evento' => $this->evento,
                'mensaje' => $this->mensaje,
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
