@extends('layouts.admin')

@section('title', 'Admin Portfolio - Mima Studio')
@section('page-title', 'Portfolio')
@section('eyebrow', 'Contenuti')

@section('actions')
    <a class="admin-btn" href="{{ route('portfolio') }}" target="_blank">Vedi pagina</a>
    <a class="admin-btn primary" href="{{ route('admin.portfolio.create') }}">Nuovo progetto</a>
@endsection

@section('content')
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Progetti</h2>
                <p class="admin-meta">{{ $projects->count() }} elementi totali</p>
            </div>
            <a class="admin-link" href="{{ route('admin.clients.index') }}">Gestisci clienti</a>
        </div>

        @if ($projects->isNotEmpty())
            <div class="admin-table">
                <div class="admin-table-head">
                    <span>Cover</span>
                    <span>Progetto</span>
                    <span>Stato</span>
                    <span>Ordine</span>
                    <span>Azioni</span>
                </div>

                @foreach ($projects as $project)
                    <div class="admin-row">
                        <img class="admin-thumb" src="{{ $project->coverImageUrl() }}" alt="{{ $project->title }}">
                        <div class="admin-title">
                            <h3>{{ $project->title }}</h3>
                            <p class="admin-meta">{{ $project->category ?: 'Senza categoria' }}</p>
                        </div>
                        <span @class(['admin-status', 'is-hidden' => ! $project->is_published])>
                            {{ $project->is_published ? 'Pubblicato' : 'Nascosto' }}
                        </span>
                        <span>{{ $project->sort_order }}</span>
                        <div class="admin-actions">
                            <a class="admin-btn" href="{{ route('admin.portfolio.edit', $project) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-icon lucide-pencil"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>
                            </a>
                            <form action="{{ route('admin.portfolio.destroy', $project) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="admin-danger" type="submit" onclick="return confirm('Eliminare questo progetto?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-icon lucide-trash"><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="admin-empty">Nessun progetto inserito.</div>
        @endif
    </section>
@endsection
