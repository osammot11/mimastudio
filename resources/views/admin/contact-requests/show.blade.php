@extends('layouts.admin')

@section('title', 'Richiesta di '.$contactRequest->full_name.' - Admin')
@section('page-title', $contactRequest->full_name)
@section('eyebrow', 'Richiesta del '.$contactRequest->created_at->format('d/m/Y'))

@section('actions')
    <a class="admin-btn" href="{{ route('admin.contact-requests.index') }}">Torna alle richieste</a>
@endsection

@section('content')
    <div class="admin-form">
        <section class="admin-card admin-form-section">
            <h2>Contatto</h2>
            <div class="admin-grid-2">
                <div>
                    <p class="admin-meta">Email</p>
                    <a class="admin-link" href="mailto:{{ $contactRequest->email }}">{{ $contactRequest->email }}</a>
                </div>
                <div>
                    <p class="admin-meta">Telefono</p>
                    <a class="admin-link" href="tel:{{ $contactRequest->phone }}">{{ $contactRequest->phone }}</a>
                </div>
            </div>
        </section>

        <section class="admin-card admin-form-section">
            <h2>Progetto</h2>
            <div class="admin-grid-2">
                <div>
                    <p class="admin-meta">Tipo</p>
                    <p>{{ ucfirst($contactRequest->project_type) }}</p>
                </div>
                <div>
                    <p class="admin-meta">Consenso privacy</p>
                    <p>{{ $contactRequest->privacy_accepted_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div>
                <p class="admin-meta">Messaggio</p>
                <p>{!! nl2br(e($contactRequest->message)) !!}</p>
            </div>
        </section>

        @if ($contactRequest->project_type === 'matrimoni')
            <section class="admin-card admin-form-section">
                <h2>Dettagli matrimonio</h2>
                <div class="admin-grid-2">
                    <div><p class="admin-meta">Data</p><p>{{ $contactRequest->wedding_date?->format('d/m/Y') }}</p></div>
                    <div><p class="admin-meta">Orario</p><p>{{ $contactRequest->wedding_time ?: 'Non indicato' }}</p></div>
                    <div><p class="admin-meta">Cerimonia</p><p>{{ ucfirst($contactRequest->ceremony_type) }}</p></div>
                    <div><p class="admin-meta">Location</p><p>{{ $contactRequest->reception_location }}</p></div>
                    <div><p class="admin-meta">Invitati</p><p>{{ $contactRequest->guest_count }}</p></div>
                    <div><p class="admin-meta">Richiesta</p><p>{{ ucfirst($contactRequest->request_type) }}</p></div>
                    <div><p class="admin-meta">Come ci hanno conosciuto</p><p>{{ $contactRequest->referral_source }}</p></div>
                </div>

                <div>
                    <p class="admin-meta">Servizi aggiuntivi</p>
                    <p>{{ implode(', ', $contactRequest->additional_services ?: []) ?: 'Nessuno' }}</p>
                </div>
                <div>
                    <p class="admin-meta">Servizi premium</p>
                    <p>{{ implode(', ', $contactRequest->premium_services ?: []) ?: 'Nessuno' }}</p>
                </div>
                <div>
                    <p class="admin-meta">La loro storia</p>
                    <p>{!! nl2br(e($contactRequest->wedding_story)) !!}</p>
                </div>
            </section>
        @endif
    </div>
@endsection
