@extends('layouts.admin')

@section('title', 'Modifica progetto - Mima Studio')
@section('page-title', 'Modifica progetto')
@section('eyebrow', 'Portfolio')

@section('actions')
    <a class="admin-btn" href="{{ route('portfolio.show', $project) }}" target="_blank">Vedi scheda</a>
    <a class="admin-btn" href="{{ route('admin.portfolio.index') }}">Torna alla lista</a>
@endsection

@section('content')
    @include('admin.portfolio.form', [
        'action' => route('admin.portfolio.update', $project),
        'method' => 'PUT',
    ])
@endsection
