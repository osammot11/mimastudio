<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientWorkReady extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public ?string $accessUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Il tuo nuovo lavoro fotografico è disponibile',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-work-ready',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
