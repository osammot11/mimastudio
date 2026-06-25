<?php

namespace App\Mail;

use App\Models\WorkDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkDeliveryReady extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public WorkDelivery $workDelivery) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Il tuo lavoro fotografico è pronto',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.work-delivery-ready',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
