@extends('layouts.admin')

@section('title', 'Admin Clienti - Mima Studio')
@section('page-title', 'Clienti')
@section('eyebrow', 'Contenuti')

@section('actions')
    <a class="admin-btn" href="{{ route('clienti') }}" target="_blank">Vedi pagina</a>
    <a class="admin-btn primary" href="{{ route('admin.clients.create') }}">Nuovo cliente</a>
@endsection

@section('content')
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Clienti</h2>
                <p class="admin-meta">{{ $clients->count() }} elementi totali</p>
            </div>
            <a class="admin-link" href="{{ route('admin.portfolio.index') }}">Gestisci portfolio</a>
        </div>

        @if ($clients->isNotEmpty())
            <div class="admin-table">
                <div class="admin-table-head">
                    <span>Foto</span>
                    <span>Cliente</span>
                    <span>Stato</span>
                    <span>Ordine</span>
                    <span>Azioni</span>
                </div>

                @foreach ($clients as $client)
                    <div class="admin-row">
                        <img class="admin-thumb" src="{{ $client->photoImageUrl() }}" alt="{{ $client->name }}">
                        <div class="admin-title">
                            <h3>{{ $client->name }}</h3>
                            <p class="admin-meta">
                                {{ $client->category ?: 'Senza categoria' }} · {{ $client->client_date ? $client->client_date->format('d/m/Y') : 'Senza data' }}
                            </p>
                        </div>
                        <span @class(['admin-status', 'is-hidden' => ! $client->is_published])>
                            {{ $client->is_published ? 'Pubblicato' : 'Nascosto' }}
                        </span>
                        <span>{{ $client->sort_order }}</span>
                        <div class="admin-actions">
                            <a class="admin-btn" href="{{ route('admin.clients.edit', $client) }}">Modifica</a>
                            <form action="{{ route('admin.clients.destroy', $client) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="admin-danger" type="submit" onclick="return confirm('Eliminare questo cliente?')">Elimina</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="admin-empty">Nessun cliente inserito.</div>
        @endif
    </section>
@endsection
