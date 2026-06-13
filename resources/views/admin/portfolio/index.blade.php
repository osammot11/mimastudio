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
                            <a class="admin-btn" href="{{ route('admin.portfolio.edit', $project) }}">Modifica</a>
                            <form action="{{ route('admin.portfolio.destroy', $project) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="admin-danger" type="submit" onclick="return confirm('Eliminare questo progetto?')">Elimina</button>
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
