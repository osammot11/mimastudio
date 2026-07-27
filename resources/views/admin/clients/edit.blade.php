@extends('layouts.admin')

@section('title', 'Modifica lavoro - Mima Studio')
@section('page-title', 'Modifica lavoro')
@section('eyebrow', 'Lavori clienti')

@section('actions')
    @if ($client->is_published)
        <a class="admin-btn" href="{{ route('clienti.show', $client) }}" target="_blank">Vedi scheda pubblica</a>
    @endif
    <a class="admin-btn" href="{{ route('admin.clients.index') }}">Torna alla lista</a>
@endsection

@section('content')
    @include('admin.clients.form', [
        'action' => route('admin.clients.update', $client),
        'method' => 'PUT',
    ])
@endsection
