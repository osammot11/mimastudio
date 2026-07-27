@extends('layouts.admin')

@section('title', 'Modifica cliente - Mima Studio')
@section('page-title', 'Modifica cliente')
@section('eyebrow', 'Clienti')

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
