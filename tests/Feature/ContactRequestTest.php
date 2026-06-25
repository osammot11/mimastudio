<?php

namespace Tests\Feature;

use App\Models\ContactRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_standard_contact_request_is_saved(): void
    {
        $this->post('/contatti', [
            'nome_completo' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'telefono' => '3331234567',
            'tipo_progetto' => 'ritratti',
            'messaggio' => 'Vorrei organizzare un servizio fotografico.',
            'privacy' => '1',
        ])->assertRedirect('/contatti')
            ->assertSessionHas('contact_success');

        $this->assertDatabaseHas('contact_requests', [
            'full_name' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'project_type' => 'ritratti',
            'wedding_date' => null,
        ]);
    }

    public function test_wedding_contact_request_saves_all_details(): void
    {
        $this->post('/contatti', [
            'nome_completo' => 'Anna e Luca',
            'email' => 'anna@example.com',
            'telefono' => '3331234567',
            'tipo_progetto' => 'matrimoni',
            'data_nozze' => '2027-06-12',
            'orario_nozze' => '15:30',
            'tipo_cerimonia' => 'civile',
            'location_ricevimento' => 'Villa Test',
            'numero_invitati' => 120,
            'tipo_richiesta' => 'foto e video',
            'servizi_aggiuntivi' => ['Preparazione sposa', 'Preparazione sposo'],
            'servizi_premium' => ['Servizio Polaroid'],
            'racconto_matrimonio' => 'Una giornata semplice e luminosa.',
            'come_conosciuti' => 'Instagram',
            'messaggio' => 'Vorremmo ricevere maggiori informazioni.',
            'privacy' => '1',
        ])->assertRedirect('/contatti')
            ->assertSessionHas('contact_success');

        $contactRequest = ContactRequest::firstOrFail();

        $this->assertSame('matrimoni', $contactRequest->project_type);
        $this->assertSame('12/06/2027', $contactRequest->wedding_date->format('d/m/Y'));
        $this->assertSame(['Preparazione sposa', 'Preparazione sposo'], $contactRequest->additional_services);
        $this->assertSame(['Servizio Polaroid'], $contactRequest->premium_services);
    }

    public function test_wedding_fields_are_required_only_for_weddings(): void
    {
        $this->from('/contatti')->post('/contatti', [
            'nome_completo' => 'Anna e Luca',
            'email' => 'anna@example.com',
            'telefono' => '3331234567',
            'tipo_progetto' => 'matrimoni',
            'messaggio' => 'Richiesta matrimonio.',
            'privacy' => '1',
        ])->assertRedirect('/contatti')
            ->assertSessionHasErrors([
                'data_nozze',
                'tipo_cerimonia',
                'location_ricevimento',
                'numero_invitati',
                'tipo_richiesta',
                'racconto_matrimonio',
                'come_conosciuti',
            ]);

        $this->assertDatabaseEmpty('contact_requests');
    }

    public function test_admin_can_read_contact_requests(): void
    {
        $user = User::factory()->create();
        $contactRequest = ContactRequest::create([
            'full_name' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'phone' => '3331234567',
            'project_type' => 'brand',
            'message' => 'Nuova campagna.',
            'privacy_accepted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.contact-requests.index'))
            ->assertOk()
            ->assertSee('Mario Rossi');

        $this->actingAs($user)
            ->get(route('admin.contact-requests.show', $contactRequest))
            ->assertOk()
            ->assertSee('Nuova campagna.');

        $this->assertNotNull($contactRequest->fresh()->viewed_at);
    }
}
