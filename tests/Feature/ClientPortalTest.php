<?php

namespace Tests\Feature;

use App\Mail\ClientPortalAccess;
use App\Models\Client;
use App\Models\WorkDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ClientPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_request_a_link_and_access_their_deliveries(): void
    {
        Mail::fake();
        $delivery = $this->createDelivery('Cliente@Example.com');

        $this->post(route('client-area.send-link'), [
            'email' => 'cliente@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertSent(ClientPortalAccess::class, function (ClientPortalAccess $mail) use (&$accessUrl): bool {
            $accessUrl = $mail->accessUrl;

            return $mail->hasTo('cliente@example.com');
        });

        $this->get($accessUrl)
            ->assertRedirect(route('client-area.index'))
            ->assertSessionHas('client_portal_email', 'cliente@example.com');

        $this->get(route('client-area.index'))
            ->assertOk()
            ->assertSee($delivery->client_name)
            ->assertSee($delivery->work_description);

        $this->get(route('client-area.show', $delivery))
            ->assertOk()
            ->assertSee('Visualizza e scarica il lavoro')
            ->assertSee($delivery->gallery_url);
    }

    public function test_client_cannot_open_another_clients_delivery(): void
    {
        $ownDelivery = $this->createDelivery('cliente@example.com');
        $otherDelivery = $this->createDelivery('altro@example.com');

        $this->withSession(['client_portal_email' => 'cliente@example.com'])
            ->get(route('client-area.show', $ownDelivery))
            ->assertOk();

        $this->withSession(['client_portal_email' => 'cliente@example.com'])
            ->get(route('client-area.show', $otherDelivery))
            ->assertNotFound();
    }

    public function test_private_client_gallery_is_visible_only_in_the_correct_portal(): void
    {
        Mail::fake();
        $client = $this->createClient('cliente@example.com', false);
        $otherClient = $this->createClient('altro@example.com', false);

        $this->get(route('clienti'))
            ->assertOk()
            ->assertDontSee($client->name);
        $this->get(route('clienti.show', $client))->assertNotFound();

        $this->post(route('client-area.send-link'), [
            'email' => 'cliente@example.com',
        ])->assertSessionHas('status');

        Mail::assertSent(ClientPortalAccess::class);

        $this->withSession(['client_portal_email' => 'cliente@example.com'])
            ->get(route('client-area.index'))
            ->assertOk()
            ->assertSee($client->name)
            ->assertDontSee($otherClient->name);

        $this->withSession(['client_portal_email' => 'cliente@example.com'])
            ->get(route('client-area.clients.show', $client))
            ->assertOk()
            ->assertSee('GALLERIA PRIVATA')
            ->assertSee($client->description);

        $this->withSession(['client_portal_email' => 'cliente@example.com'])
            ->get(route('client-area.clients.show', $otherClient))
            ->assertNotFound();
    }

    public function test_client_hidden_from_portal_cannot_be_accessed_privately(): void
    {
        Mail::fake();
        $client = $this->createClient('cliente@example.com', true, false);

        $this->get(route('clienti.show', $client))
            ->assertOk()
            ->assertSee($client->name);

        $this->post(route('client-area.send-link'), [
            'email' => 'cliente@example.com',
        ])->assertSessionHas('status');

        Mail::assertNothingSent();

        $this->withSession(['client_portal_email' => 'cliente@example.com'])
            ->get(route('client-area.index'))
            ->assertOk()
            ->assertDontSee($client->name);

        $this->withSession(['client_portal_email' => 'cliente@example.com'])
            ->get(route('client-area.clients.show', $client))
            ->assertNotFound();
    }

    public function test_signed_notification_link_opens_the_private_client_gallery(): void
    {
        $client = $this->createClient('cliente@example.com', false);
        $url = URL::temporarySignedRoute(
            'client-area.authenticate',
            now()->addMinutes(10),
            [
                'token' => Crypt::encryptString('cliente@example.com'),
                'client' => $client->getKey(),
            ],
        );

        $this->get($url)
            ->assertRedirect(route('client-area.clients.show', $client))
            ->assertSessionHas('client_portal_email', 'cliente@example.com');
    }

    public function test_client_area_requires_a_valid_session(): void
    {
        $delivery = $this->createDelivery('cliente@example.com');

        $this->get(route('client-area.index'))
            ->assertRedirect(route('client-area.login'));

        $this->get(route('client-area.show', $delivery))
            ->assertRedirect(route('client-area.login'));
    }

    public function test_unknown_email_gets_the_same_response_without_sending_mail(): void
    {
        Mail::fake();

        $this->post(route('client-area.send-link'), [
            'email' => 'sconosciuto@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertNothingSent();
    }

    public function test_an_invalid_or_unsigned_access_link_is_rejected(): void
    {
        $this->createDelivery('cliente@example.com');
        $token = Crypt::encryptString('cliente@example.com');

        $this->get(route('client-area.authenticate', ['token' => $token]))
            ->assertForbidden();

        $expiredUrl = URL::temporarySignedRoute(
            'client-area.authenticate',
            now()->subMinute(),
            ['token' => $token],
        );

        $this->get($expiredUrl)->assertForbidden();
    }

    public function test_logout_removes_client_portal_access(): void
    {
        $this->withSession(['client_portal_email' => 'cliente@example.com'])
            ->post(route('client-area.logout'))
            ->assertRedirect(route('client-area.login'))
            ->assertSessionMissing('client_portal_email');
    }

    private function createDelivery(string $email): WorkDelivery
    {
        return WorkDelivery::create([
            'client_name' => 'Mario Rossi',
            'work_description' => 'Servizio fotografico completo.',
            'work_date' => '2026-07-27',
            'email' => $email,
            'gallery_url' => 'https://example.com/gallery/'.uniqid(),
        ]);
    }

    private function createClient(
        string $email,
        bool $isPublished,
        bool $isPortalVisible = true,
    ): Client {
        $slug = 'client-'.uniqid();

        return Client::create([
            'name' => 'Cliente '.$slug,
            'email' => $email,
            'slug' => $slug,
            'description' => 'Galleria fotografica privata.',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 1,
            'is_published' => $isPublished,
            'is_portal_visible' => $isPortalVisible,
        ]);
    }
}
