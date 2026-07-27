@extends('layouts.admin')

@section('title', 'Nuovo lavoro - Mima Studio')
@section('page-title', 'Nuovo lavoro')
@section('eyebrow', 'Lavori clienti')

@section('actions')
    <a class="admin-btn" href="{{ route('admin.clients.index') }}">Torna alla lista</a>
@endsection

@section('content')
    @include('admin.clients.form', [
        'action' => route('admin.clients.store'),
        'method' => 'POST',
    ])
@endsection
