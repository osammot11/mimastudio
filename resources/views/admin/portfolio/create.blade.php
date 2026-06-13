@extends('layouts.admin')

@section('title', 'Nuovo progetto - Mima Studio')
@section('page-title', 'Nuovo progetto')
@section('eyebrow', 'Portfolio')

@section('actions')
    <a class="admin-btn" href="{{ route('admin.portfolio.index') }}">Torna alla lista</a>
@endsection

@section('content')
    @include('admin.portfolio.form', [
        'action' => route('admin.portfolio.store'),
        'method' => 'POST',
    ])
@endsection
