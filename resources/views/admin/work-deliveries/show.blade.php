@extends('layouts.admin')

@section('title', 'Consegna di '.$workDelivery->client_name.' - Admin')
@section('page-title', $workDelivery->client_name)
@section('eyebrow', 'Consegna del '.$workDelivery->work_date->format('d/m/Y'))

@section('actions')
    <a class="admin-btn" href="{{ route('admin.work-deliveries.index') }}">Torna alle consegne</a>
    <form action="{{ route('admin.work-deliveries.resend', $workDelivery) }}" method="post">
        @csrf
        <button class="admin-btn primary" type="submit">
            {{ $workDelivery->sent_at ? 'Reinvia email' : 'Riprova invio' }}
        </button>
    </form>
@endsection

@section('content')
    <div class="admin-form">
        <section class="admin-card admin-form-section">
            <h2>Riepilogo</h2>

            <div class="admin-grid-2">
                <div>
                    <p class="admin-meta">Email cliente</p>
                    <a class="admin-link" href="mailto:{{ $workDelivery->email }}">{{ $workDelivery->email }}</a>
                </div>
                <div>
                    <p class="admin-meta">Codice identificativo</p>
                    <p>{{ $workDelivery->identifier_code ?: 'Non indicato' }}</p>
                </div>
                <div>
                    <p class="admin-meta">Stato email</p>
                    <p>{{ $workDelivery->sent_at ? 'Inviata il '.$workDelivery->sent_at->format('d/m/Y H:i') : 'Non inviata' }}</p>
                </div>
                <div>
                    <p class="admin-meta">Data lavoro</p>
                    <p>{{ $workDelivery->work_date->format('d/m/Y') }}</p>
                </div>
            </div>

            <div>
                <p class="admin-meta">Descrizione lavoro</p>
                <p>{!! nl2br(e($workDelivery->work_description)) !!}</p>
            </div>

            <div>
                <p class="admin-meta">Link al lavoro</p>
                <a class="admin-link" href="{{ $workDelivery->gallery_url }}" target="_blank" rel="noopener">
                    Apri galleria esterna
                </a>
                <p class="admin-help admin-break-word">{{ $workDelivery->gallery_url }}</p>
            </div>
        </section>

        @if ($workDelivery->last_send_error)
            <section class="admin-card admin-form-section">
                <h2>Ultimo errore di invio</h2>
                <p class="admin-error-text">{{ $workDelivery->last_send_error }}</p>
            </section>
        @endif
    </div>
@endsection
