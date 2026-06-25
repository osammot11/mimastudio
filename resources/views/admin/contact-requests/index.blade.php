@extends('layouts.admin')

@section('title', 'Richieste - Admin Mima Studio')
@section('page-title', 'Richieste')
@section('eyebrow', 'Contatti')

@section('content')
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Richieste ricevute</h2>
                <p class="admin-meta">{{ $contactRequests->total() }} richieste totali</p>
            </div>
        </div>

        @if ($contactRequests->isNotEmpty())
            <div class="admin-table admin-requests">
                <div class="admin-table-head">
                    <span>Data</span>
                    <span>Contatto</span>
                    <span>Progetto</span>
                    <span>Stato</span>
                    <span>Azioni</span>
                </div>

                @foreach ($contactRequests as $contactRequest)
                    <div class="admin-row">
                        <span>{{ $contactRequest->created_at->format('d/m/Y H:i') }}</span>
                        <div class="admin-title">
                            <h3>{{ $contactRequest->full_name }}</h3>
                            <p class="admin-meta">{{ $contactRequest->email }}</p>
                        </div>
                        <span>{{ ucfirst($contactRequest->project_type) }}</span>
                        <span @class(['admin-status', 'is-hidden' => $contactRequest->viewed_at])>
                            {{ $contactRequest->viewed_at ? 'Letta' : 'Nuova' }}
                        </span>
                        <div class="admin-actions">
                            <a class="admin-btn" href="{{ route('admin.contact-requests.show', $contactRequest) }}">Apri</a>
                            <form action="{{ route('admin.contact-requests.destroy', $contactRequest) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="admin-danger" type="submit" onclick="return confirm('Eliminare questa richiesta?')">Elimina</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="admin-card-body">
                {{ $contactRequests->links() }}
            </div>
        @else
            <div class="admin-empty">Nessuna richiesta ricevuta.</div>
        @endif
    </section>
@endsection
