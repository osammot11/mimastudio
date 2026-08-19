<?php

namespace App\Services;

use App\Mail\ClientWorkReady;
use App\Models\Client;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use RuntimeException;

class ClientWorkNotifier
{
    public function send(Client $client): void
    {
        $client->loadMissing('customer');

        if (! $client->customer?->email) {
            throw new RuntimeException('Completa prima l’indirizzo email del cliente.');
        }

        Mail::to($client->customer->email)->send(
            new ClientWorkReady($client, $this->accessUrl($client)),
        );
    }

    public function accessUrl(Client $client): ?string
    {
        $client->loadMissing('customer');

        if ($client->is_portal_visible) {
            return URL::temporarySignedRoute(
                'client-area.authenticate',
                now()->addDays(7),
                [
                    'token' => Crypt::encryptString($client->customer->email),
                    'client' => $client->getKey(),
                ],
            );
        }

        return $client->is_published
            ? route('clienti.show', $client)
            : null;
    }
}
