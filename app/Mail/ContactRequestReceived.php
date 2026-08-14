<?php

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactRequest $contactRequest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address(
                    $this->contactRequest->email,
                    $this->contactRequest->full_name,
                ),
            ],
            subject: 'Nuova richiesta: '.ucfirst($this->contactRequest->project_type),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-request-received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
