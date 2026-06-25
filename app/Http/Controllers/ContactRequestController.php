<?php

namespace App\Http\Controllers;

use App\Models\ContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $isWedding = $request->input('tipo_progetto') === 'matrimoni';

        $data = $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['required', 'string', 'max:50'],
            'tipo_progetto' => ['required', Rule::in([
                'ritratti',
                'cerimonie',
                'matrimoni',
                'brand',
                'grandi eventi',
                'eventi aziendali',
                'altro',
            ])],
            'messaggio' => ['required', 'string', 'max:5000'],
            'data_nozze' => [Rule::requiredIf($isWedding), 'nullable', 'date'],
            'orario_nozze' => ['nullable', 'date_format:H:i'],
            'tipo_cerimonia' => [Rule::requiredIf($isWedding), 'nullable', Rule::in(['religiosa', 'civile'])],
            'location_ricevimento' => [Rule::requiredIf($isWedding), 'nullable', 'string', 'max:255'],
            'numero_invitati' => [Rule::requiredIf($isWedding), 'nullable', 'integer', 'min:1', 'max:5000'],
            'tipo_richiesta' => [Rule::requiredIf($isWedding), 'nullable', Rule::in(['foto', 'foto e video'])],
            'servizi_aggiuntivi' => ['nullable', 'array'],
            'servizi_aggiuntivi.*' => ['string', Rule::in([
                'Preparazione sposa',
                'Preparazione sposo',
                'Festeggiamenti e balli (post taglio della torta)',
                'Solo cerimonia, posato e rinfresco (fino al taglio della torta, balli esclusi)',
            ])],
            'servizi_premium' => ['nullable', 'array'],
            'servizi_premium.*' => ['string', Rule::in([
                'Bomboniere fotografiche',
                'Servizio Polaroid',
                'Stampe via WhatsApp',
                'Servizio prematrimoniale',
                'Servizio post-matrimoniale',
            ])],
            'racconto_matrimonio' => [Rule::requiredIf($isWedding), 'nullable', 'string', 'max:5000'],
            'come_conosciuti' => [Rule::requiredIf($isWedding), 'nullable', Rule::in([
                'Instagram',
                'Facebook',
                'Google',
                'Matrimonio.com',
                'Passaparola',
                'Evento o fiera',
                'Altro',
            ])],
            'privacy' => ['accepted'],
        ]);

        ContactRequest::create([
            'full_name' => $data['nome_completo'],
            'email' => $data['email'],
            'phone' => $data['telefono'],
            'project_type' => $data['tipo_progetto'],
            'message' => $data['messaggio'],
            'wedding_date' => $isWedding ? ($data['data_nozze'] ?? null) : null,
            'wedding_time' => $isWedding ? ($data['orario_nozze'] ?? null) : null,
            'ceremony_type' => $isWedding ? ($data['tipo_cerimonia'] ?? null) : null,
            'reception_location' => $isWedding ? ($data['location_ricevimento'] ?? null) : null,
            'guest_count' => $isWedding ? ($data['numero_invitati'] ?? null) : null,
            'request_type' => $isWedding ? ($data['tipo_richiesta'] ?? null) : null,
            'additional_services' => $isWedding ? ($data['servizi_aggiuntivi'] ?? []) : null,
            'premium_services' => $isWedding ? ($data['servizi_premium'] ?? []) : null,
            'wedding_story' => $isWedding ? ($data['racconto_matrimonio'] ?? null) : null,
            'referral_source' => $isWedding ? ($data['come_conosciuti'] ?? null) : null,
            'privacy_accepted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('contatti')
            ->with('contact_success', 'Richiesta inviata. Ti ricontatterò appena possibile.');
    }
}
