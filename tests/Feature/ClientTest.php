<?php

namespace Tests\Feature;

use App\Mail\ClientWorkReady;
use App\Models\Client;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_clients_index_shows_only_published_clients(): void
    {
        $customer = $this->createCustomer('index@example.com');

        Client::create([
            'customer_id' => $customer->id,
            'name' => 'Visible Client',
            'slug' => 'visible-client',
            'description' => 'Visible description',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Client::create([
            'customer_id' => $customer->id,
            'name' => 'Hidden Client',
            'slug' => 'hidden-client',
            'description' => 'Hidden description',
            'photo_image' => 'images/portfolio-3.jpeg',
            'cover_image' => 'images/portfolio-4.jpeg',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $this->get('/clienti')
            ->assertOk()
            ->assertSee('Visible Client')
            ->assertDontSee('Hidden Client');
    }

    public function test_clients_index_can_be_filtered_by_category(): void
    {
        $customer = $this->createCustomer('filters@example.com');

        Client::create([
            'customer_id' => $customer->id,
            'name' => 'Brand Client',
            'slug' => 'brand-client',
            'description' => 'Brand description',
            'category' => 'Brand',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Client::create([
            'customer_id' => $customer->id,
            'name' => 'Event Client',
            'slug' => 'event-client',
            'description' => 'Event description',
            'category' => 'Eventi',
            'photo_image' => 'images/portfolio-3.jpeg',
            'cover_image' => 'images/portfolio-4.jpeg',
            'sort_order' => 2,
            'is_published' => true,
        ]);

        $this->get('/clienti?categoria=brand')
            ->assertOk()
            ->assertSee('Brand Client')
            ->assertDontSee('Event Client');
    }

    public function test_published_client_detail_is_visible_and_hidden_client_is_not(): void
    {
        $customer = $this->createCustomer('details@example.com');

        Client::create([
            'customer_id' => $customer->id,
            'name' => 'Visible Client',
            'slug' => 'visible-client',
            'description' => 'Visible description',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Client::create([
            'customer_id' => $customer->id,
            'name' => 'Hidden Client',
            'slug' => 'hidden-client',
            'description' => 'Hidden description',
            'photo_image' => 'images/portfolio-3.jpeg',
            'cover_image' => 'images/portfolio-4.jpeg',
            'sort_order' => 2,
            'is_published' => false,
        ]);

        $this->get('/clienti/visible-client')
            ->assertOk()
            ->assertSee('Visible Client');

        $this->get('/clienti/hidden-client')
            ->assertNotFound();
    }

    public function test_admin_can_create_client(): void
    {
        Mail::fake();
        Storage::fake('public');

        $user = User::factory()->create([
            'password' => 'password',
        ]);
        $customer = $this->createCustomer('cliente@example.com');

        $this->actingAs($user)->post('/admin/clients', [
            'customer_mode' => 'existing',
            'customer_id' => $customer->id,
            'name' => 'Admin Work',
            'description' => 'Created from admin',
            'client_date' => '2026-06-10',
            'sort_order' => 1,
            'is_published' => '1',
            'is_portal_visible' => '1',
            'photo_image' => UploadedFile::fake()->image('photo.jpg'),
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ])->assertRedirect();

        $this->assertDatabaseHas('clients', [
            'customer_id' => $customer->id,
            'name' => 'Admin Work',
            'slug' => 'admin-work',
            'is_published' => true,
            'is_portal_visible' => true,
        ]);

        Mail::assertNothingSent();
    }

    public function test_admin_can_notify_client_about_a_new_private_work(): void
    {
        Mail::fake();
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.clients.store'), [
            'customer_mode' => 'new',
            'new_customer_name' => 'Notified Customer',
            'new_customer_email' => 'NOTIFY@example.com',
            'name' => 'Notified Work',
            'description' => 'Nuovo servizio fotografico.',
            'sort_order' => 1,
            'send_notification' => '1',
            'is_portal_visible' => '1',
            'photo_image' => UploadedFile::fake()->image('photo.jpg'),
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ])->assertRedirect();

        Mail::assertSent(ClientWorkReady::class, function (ClientWorkReady $mail): bool {
            return $mail->hasTo('notify@example.com')
                && str_contains((string) $mail->accessUrl, '/area-clienti/accesso');
        });

        $this->assertDatabaseHas('customers', [
            'name' => 'Notified Customer',
            'email' => 'notify@example.com',
        ]);
    }

    public function test_admin_can_make_a_client_private_only(): void
    {
        $user = User::factory()->create();
        $customer = $this->createCustomer('private@example.com');
        $client = Client::create([
            'customer_id' => $customer->id,
            'name' => 'Private Client',
            'slug' => 'private-client',
            'description' => 'Private description',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($user)->put(route('admin.clients.update', $client), [
            'customer_mode' => 'existing',
            'customer_id' => $customer->id,
            'name' => $client->name,
            'slug' => $client->slug,
            'description' => $client->description,
            'sort_order' => $client->sort_order,
            'is_portal_visible' => '1',
        ])->assertRedirect(route('admin.clients.edit', $client));

        $this->assertFalse($client->fresh()->is_published);
        $this->assertTrue($client->fresh()->is_portal_visible);
        $this->get(route('clienti.show', $client))->assertNotFound();
    }

    public function test_multiple_works_can_use_the_same_existing_customer(): void
    {
        Mail::fake();
        Storage::fake('public');
        $user = User::factory()->create();
        $customer = $this->createCustomer('same@example.com');

        foreach (['Primo lavoro', 'Secondo lavoro'] as $name) {
            $this->actingAs($user)->post(route('admin.clients.store'), [
                'customer_mode' => 'existing',
                'customer_id' => $customer->id,
                'name' => $name,
                'description' => 'Descrizione lavoro.',
                'sort_order' => 1,
                'is_portal_visible' => '1',
                'photo_image' => UploadedFile::fake()->image($name.'-photo.jpg'),
                'cover_image' => UploadedFile::fake()->image($name.'-cover.jpg'),
            ])->assertRedirect();
        }

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('clients', 2);
        $this->assertSame(
            [$customer->id],
            Client::query()->distinct()->pluck('customer_id')->all(),
        );
    }

    private function createCustomer(string $email): Customer
    {
        return Customer::create([
            'name' => 'Test Customer',
            'email' => $email,
        ]);
    }
}
