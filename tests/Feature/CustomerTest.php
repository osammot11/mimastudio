<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_customer_admin_requires_authentication(): void
    {
        $this->get(route('admin.customers.index'))
            ->assertRedirect(route('admin.login'));
    }
}
