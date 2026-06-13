@extends('layouts.admin')

@section('title', 'Admin Login - Mima Studio')
@section('page-title', 'Accedi al portale')
@section('eyebrow', 'Admin')

@section('content')
    <div class="admin-login">
        <section class="admin-card">
            <div class="admin-card-header">
                <div>
                    <h2>Login</h2>
                    <p class="admin-meta">Inserisci le credenziali admin.</p>
                </div>
            </div>

            <form class="admin-form admin-card-body" action="{{ route('admin.login.store') }}" method="post">
                @csrf

                <div class="admin-field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                </div>

                <div class="admin-field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" required>
                </div>

                <label class="admin-check">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ricordami</span>
                </label>

                @if ($errors->any())
                    <div class="admin-errors">
                        <p>{{ $errors->first() }}</p>
                    </div>
                @endif

                <button class="admin-btn primary" type="submit">Entra</button>
            </form>
        </section>
    </div>
@endsection
