@extends('layouts.app')

@section('title', 'Area clienti - Michele Mariani')

@section('content')
    <section class="section-standard bottom-line client-portal-section">
        <div class="wrapper grid-2">
            <div class="stack-large text-container">
                <p class="pill">AREA PRIVATA</p>
                <h1>I tuoi lavori,<br>in un unico spazio.</h1>
                <p>Inserisci l'indirizzo email associato al tuo lavoro. Riceverai un link personale per accedere a gallerie e consegne.</p>
            </div>

            <div class="contact-wizard contact-form">
                @if (session('status'))
                    <div class="contact-success">
                        <p>{{ session('status') }}</p>
                    </div>
                @endif

                @if (session('status_error'))
                    <div class="contact-server-errors">
                        <p>{{ session('status_error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="contact-server-errors">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form class="stack-xl" action="{{ route('client-area.send-link') }}" method="post">
                    @csrf

                    <label>
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="nome@email.it" autocomplete="email" required autofocus>
                    </label>

                    <button class="btn-2" type="submit">Ricevi il link di accesso</button>
                </form>
            </div>
        </div>
    </section>
@endsection
