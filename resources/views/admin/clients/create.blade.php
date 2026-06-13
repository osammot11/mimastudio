@extends('layouts.admin')

@section('title', 'Nuovo cliente - Mima Studio')
@section('page-title', 'Nuovo cliente')
@section('eyebrow', 'Clienti')

@section('actions')
    <a class="admin-btn" href="{{ route('admin.clients.index') }}">Torna alla lista</a>
@endsection

@section('content')
    @include('admin.clients.form', [
        'action' => route('admin.clients.store'),
        'method' => 'POST',
    ])
@endsection
