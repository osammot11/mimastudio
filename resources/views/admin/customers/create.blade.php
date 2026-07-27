@extends('layouts.admin')

@section('title', 'Nuovo cliente - Mima Studio')
@section('page-title', 'Nuovo cliente')
@section('eyebrow', 'Anagrafica clienti')

@section('actions')
    <a class="admin-btn" href="{{ route('admin.customers.index') }}">Torna all'anagrafica</a>
@endsection

@section('content')
    @include('admin.customers.form', [
        'action' => route('admin.customers.store'),
        'method' => 'POST',
    ])
@endsection
