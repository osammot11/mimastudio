<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientPortalAccess extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $accessUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accedi alla tua area privata',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-portal-access',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
