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
                            <a class="admin-btn" href="{{ route('admin.clients.edit', $client) }}">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                            </a>
                            <form action="{{ route('admin.clients.destroy', $client) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="admin-danger" type="submit" onclick="return confirm('Eliminare questo cliente?')">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-icon lucide-trash"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
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
