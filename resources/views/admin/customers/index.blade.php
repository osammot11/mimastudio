@extends('layouts.admin')

@section('title', 'Anagrafica clienti - Mima Studio')
@section('page-title', 'Anagrafica clienti')
@section('eyebrow', 'Clienti')

@section('actions')
    <a class="admin-btn primary" href="{{ route('admin.customers.create') }}">Nuovo cliente</a>
@endsection

@section('content')
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Clienti registrati</h2>
                <p class="admin-meta">{{ $customers->count() }} clienti totali</p>
            </div>
            <a class="admin-link" href="{{ route('admin.clients.index') }}">Gestisci lavori</a>
        </div>

        @if ($customers->isNotEmpty())
            <div class="admin-table admin-customers">
                <div class="admin-table-head">
                    <span>Cliente</span>
                    <span>Email</span>
                    <span>Lavori</span>
                    <span>Azioni</span>
                </div>

                @foreach ($customers as $customer)
                    <div class="admin-row">
                        <div class="admin-title">
                            <h3>{{ $customer->name }}</h3>
                        </div>
                        <span>{{ $customer->email ?: 'Email da inserire' }}</span>
                        <span>{{ $customer->works_count }}</span>
                        <div class="admin-actions">
                            <a class="admin-btn" href="{{ route('admin.customers.edit', $customer) }}">Modifica</a>
                            <a class="admin-btn" href="{{ route('admin.clients.create', ['customer' => $customer->id]) }}">Nuovo lavoro</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="admin-empty">Nessun cliente registrato.</div>
        @endif
    </section>
@endsection
