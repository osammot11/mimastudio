<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_a_customer(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.customers.store'), [
            'name' => 'Mario Rossi',
            'email' => 'MARIO@Example.com',
        ])->assertRedirect();

        $customer = Customer::firstOrFail();

        $this->assertSame('mario@example.com', $customer->email);

        $this->actingAs($user)->put(route('admin.customers.update', $customer), [
            'name' => 'Mario e Lucia',
            'email' => 'famiglia@example.com',
        ])->assertRedirect(route('admin.customers.edit', $customer));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Mario e Lucia',
            'email' => 'famiglia@example.com',
        ]);
    }

    public function test_customer_email_must_be_unique(): void
    {
        $user = User::factory()->create();
        Customer::create([
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
        ]);

        $this->actingAs($user)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), [
                'name' => 'Altro Mario',
                'email' => 'MARIO@example.com',
            ])
            ->assertRedirect(route('admin.customers.create'))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('customers', 1);
    }

    public function test_admin_can_upload_replace_and_remove_a_customer_png_logo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.customers.store'), [
            'name' => 'Brand Uno',
            'email' => 'brand@example.com',
            'logo' => UploadedFile::fake()->image('brand.png', 400, 200),
        ])->assertRedirect();

        $customer = Customer::firstOrFail();
        $firstLogo = $customer->logo_path;

        $this->assertNotNull($firstLogo);
        Storage::disk('public')->assertExists($firstLogo);

        $this->actingAs($user)->put(route('admin.customers.update', $customer), [
            'name' => $customer->name,
            'email' => $customer->email,
            'logo' => UploadedFile::fake()->image('nuovo-logo.png', 400, 200),
        ])->assertRedirect();

        $customer->refresh();
        Storage::disk('public')->assertMissing($firstLogo);
        Storage::disk('public')->assertExists($customer->logo_path);

        $lastLogo = $customer->logo_path;

        $this->actingAs($user)->put(route('admin.customers.update', $customer), [
            'name' => $customer->name,
            'email' => $customer->email,
            'remove_logo' => '1',
        ])->assertRedirect();

        $this->assertNull($customer->fresh()->logo_path);
        Storage::disk('public')->assertMissing($lastLogo);
    }

    public function test_customer_logo_must_be_a_png(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('admin.customers.create'))
            ->post(route('admin.customers.store'), [
                'name' => 'Brand JPG',
                'email' => 'jpg@example.com',
                'logo' => UploadedFile::fake()->image('brand.jpg'),
            ])
            ->assertRedirect(route('admin.customers.create'))
            ->assertSessionHasErrors('logo');

        $this->assertDatabaseCount('customers', 0);
    }

    public function test_updating_customer_email_keeps_all_works_linked(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Mario Rossi',
            'email' => 'old@example.com',
        ]);
        $work = Client::create([
            'customer_id' => $customer->id,
            'name' => 'Matrimonio',
            'slug' => 'matrimonio',
            'description' => 'Servizio fotografico.',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'is_published' => false,
            'is_portal_visible' => true,
        ]);

        $this->actingAs($user)->put(route('admin.customers.update', $customer), [
            'name' => $customer->name,
            'email' => 'new@example.com',
        ])->assertRedirect();

        $this->assertSame($customer->id, $work->fresh()->customer_id);

        $this->post(route('client-area.send-link'), [
            'email' => 'old@example.com',
        ])->assertSessionHas('status');

        $this->post(route('client-area.send-link'), [
            'email' => 'new@example.com',
        ])->assertSessionHas('status');
    }

    public function test_admin_can_copy_a_valid_customer_portal_access_link(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
        ]);
        Client::create([
            'customer_id' => $customer->id,
            'name' => 'Matrimonio',
            'slug' => 'matrimonio-link-portale',
            'description' => 'Servizio fotografico.',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'is_published' => false,
            'is_portal_visible' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.customer-access-links.index'))
            ->assertOk()
            ->assertSee('Mario Rossi')
            ->assertSee('Copia link');

        preg_match(
            '/id="portal-link-'.$customer->id.'"[^>]+value="([^"]+)"/s',
            $response->getContent(),
            $matches,
        );

        $this->assertArrayHasKey(1, $matches);
        $accessUrl = html_entity_decode($matches[1], ENT_QUOTES);

        $this->get($accessUrl)
            ->assertRedirect(route('client-area.index'))
            ->assertSessionHas('client_portal_email', 'mario@example.com')
            ->assertSessionHas('client_portal_customer_id', $customer->id);
    }

    public function test_portal_link_is_not_generated_without_private_content(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Cliente senza portale',
            'email' => 'nessun-portale@example.com',
        ]);
        Client::create([
            'customer_id' => $customer->id,
            'name' => 'Lavoro nascosto',
            'slug' => 'lavoro-nascosto-portale',
            'description' => 'Servizio fotografico.',
            'photo_image' => 'images/portfolio-1.jpeg',
            'cover_image' => 'images/portfolio-2.jpeg',
            'is_published' => true,
            'is_portal_visible' => false,
        ]);

        $this->actingAs($user)
            ->get(route('admin.customer-access-links.index'))
            ->assertOk()
            ->assertSee('Non disponibile')
            ->assertDontSee('portal-link-'.$customer->id, false);
    }

    public function test_customer_admin_requires_authentication(): void
    {
        $this->get(route('admin.customers.index'))
            ->assertRedirect(route('admin.login'));

        $this->get(route('admin.customer-access-links.index'))
            ->assertRedirect(route('admin.login'));
    }
}
