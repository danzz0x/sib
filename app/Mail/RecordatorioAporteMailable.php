<?php

namespace App\Mail;

use App\Models\Socio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioAporteMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Socio $socio,
        public string $nombreMes
    ) {

        $this->connection = 'database';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recordatorio: Su aporte de '.$this->nombreMes.' está por vencer',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recordatorio-aporte',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
