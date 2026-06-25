@extends('layouts.admin')

@section('title', 'Consegna lavoro - Admin Mima Studio')
@section('page-title', 'Consegna lavoro')
@section('eyebrow', 'Clienti')

@section('actions')
    <a class="admin-btn primary" href="{{ route('admin.work-deliveries.create') }}">Nuova consegna</a>
@endsection

@section('content')
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Consegne</h2>
                <p class="admin-meta">{{ $workDeliveries->total() }} consegne totali</p>
            </div>
        </div>

        @if ($workDeliveries->isNotEmpty())
            <div class="admin-table admin-deliveries">
                <div class="admin-table-head">
                    <span>Data</span>
                    <span>Cliente</span>
                    <span>Codice</span>
                    <span>Stato</span>
                    <span>Azioni</span>
                </div>

                @foreach ($workDeliveries as $workDelivery)
                    <div class="admin-row">
                        <span>{{ $workDelivery->work_date->format('d/m/Y') }}</span>
                        <div class="admin-title">
                            <h3>{{ $workDelivery->client_name }}</h3>
                            <p class="admin-meta">{{ $workDelivery->email }}</p>
                        </div>
                        <span>{{ $workDelivery->identifier_code ?: '—' }}</span>
                        <span @class(['admin-status', 'is-failed' => $workDelivery->last_send_error])>
                            @if ($workDelivery->last_send_error)
                                Ultimo invio fallito
                            @elseif ($workDelivery->sent_at)
                                Inviata
                            @else
                                Da inviare
                            @endif
                        </span>
                        <div class="admin-actions">
                            <a class="admin-btn" href="{{ route('admin.work-deliveries.show', $workDelivery) }}">Apri</a>
                            <form action="{{ route('admin.work-deliveries.destroy', $workDelivery) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="admin-danger" type="submit" onclick="return confirm('Eliminare questa consegna?')">
                                    Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="admin-card-body">
                {{ $workDeliveries->links() }}
            </div>
        @else
            <div class="admin-empty">Nessuna consegna inserita.</div>
        @endif
    </section>
@endsection
