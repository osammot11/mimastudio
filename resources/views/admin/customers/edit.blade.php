@extends('layouts.admin')

@section('title', 'Modifica cliente - Mima Studio')
@section('page-title', $customer->name)
@section('eyebrow', 'Anagrafica clienti')

@section('actions')
    <a class="admin-btn primary" href="{{ route('admin.clients.create', ['customer' => $customer->id]) }}">Nuovo lavoro</a>
    <a class="admin-btn" href="{{ route('admin.customers.index') }}">Torna all'anagrafica</a>
@endsection

@section('content')
    <div class="admin-form">
        @include('admin.customers.form', [
            'action' => route('admin.customers.update', $customer),
            'method' => 'PUT',
        ])

        @if ($customer->works->isNotEmpty())
            <section class="admin-card admin-form-section">
                <h2>Lavori associati</h2>

                @foreach ($customer->works as $work)
                    <div class="admin-row admin-customer-work">
                        <div class="admin-title">
                            <h3>{{ $work->name }}</h3>
                            <p class="admin-meta">{{ $work->client_date?->format('d/m/Y') ?: 'Senza data' }}</p>
                        </div>
                        <a class="admin-btn" href="{{ route('admin.clients.edit', $work) }}">Apri lavoro</a>
                    </div>
                @endforeach
            </section>
        @endif
    </div>
@endsection
