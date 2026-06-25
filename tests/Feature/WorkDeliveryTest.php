<?php

namespace Tests\Feature;

use App\Mail\WorkDeliveryReady;
use App\Models\User;
use App\Models\WorkDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WorkDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_email_a_work_delivery(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.work-deliveries.store'), [
            'client_name' => 'Mario Rossi',
            'work_description' => 'Servizio fotografico completo della cerimonia.',
            'work_date' => '2026-06-25',
            'identifier_code' => 'MM-2026-001',
            'email' => 'mario@example.com',
            'gallery_url' => 'https://example.com/gallery/mario',
        ]);

        $workDelivery = WorkDelivery::firstOrFail();

        $response
            ->assertRedirect(route('admin.work-deliveries.show', $workDelivery))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('work_deliveries', [
            'client_name' => 'Mario Rossi',
            'identifier_code' => 'MM-2026-001',
            'email' => 'mario@example.com',
            'gallery_url' => 'https://example.com/gallery/mario',
        ]);
        $this->assertNotNull($workDelivery->fresh()->sent_at);

        Mail::assertSent(WorkDeliveryReady::class, function (WorkDeliveryReady $mail): bool {
            return $mail->hasTo('mario@example.com')
                && $mail->workDelivery->gallery_url === 'https://example.com/gallery/mario';
        });

        $this->actingAs($user)
            ->get(route('admin.work-deliveries.index'))
            ->assertOk()
            ->assertSee('Mario Rossi')
            ->assertSee('Inviata');

        $this->actingAs($user)
            ->get(route('admin.work-deliveries.show', $workDelivery))
            ->assertOk()
            ->assertSee('Apri galleria esterna');

        (new WorkDeliveryReady($workDelivery))
            ->assertSeeInHtml('il tuo lavoro è pronto.')
            ->assertSeeInHtml('MM-2026-001')
            ->assertSeeInHtml('https://example.com/gallery/mario');
    }

    public function test_admin_can_resend_a_work_delivery_email(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $workDelivery = WorkDelivery::create([
            'client_name' => 'Mario Rossi',
            'work_description' => 'Servizio fotografico.',
            'work_date' => '2026-06-25',
            'email' => 'mario@example.com',
            'gallery_url' => 'https://example.com/gallery/mario',
        ]);

        $this->actingAs($user)
            ->post(route('admin.work-deliveries.resend', $workDelivery))
            ->assertRedirect(route('admin.work-deliveries.show', $workDelivery))
            ->assertSessionHas('status');

        $this->assertNotNull($workDelivery->fresh()->sent_at);
        Mail::assertSent(WorkDeliveryReady::class, 1);
    }

    public function test_work_delivery_requires_a_valid_http_link(): void
    {
        Mail::fake();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('admin.work-deliveries.create'))
            ->post(route('admin.work-deliveries.store'), [
                'client_name' => 'Mario Rossi',
                'work_description' => 'Servizio fotografico.',
                'work_date' => '2026-06-25',
                'email' => 'mario@example.com',
                'gallery_url' => 'javascript:alert(1)',
            ])
            ->assertRedirect(route('admin.work-deliveries.create'))
            ->assertSessionHasErrors('gallery_url');

        $this->assertDatabaseEmpty('work_deliveries');
        Mail::assertNothingSent();
    }

    public function test_work_delivery_admin_requires_authentication(): void
    {
        $this->get(route('admin.work-deliveries.index'))
            ->assertRedirect(route('admin.login'));
    }
}
