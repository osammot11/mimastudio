<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accesso riservato - Michele Mariani</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>

<body>
    <main class="fullheight center-items section-standard">
        <div class="text-container-center center-text stack-large fullwidth">
            <p class="pill">ANTEPRIMA</p>
            <div class="stack-mid">
                <h2>Sito in preparazione.</h2>
                <p>Inserisci il codice per accedere al sito di Michele Mariani.</p>
            </div>

            <form class="contact-form stack-mid" method="POST" action="{{ route('site-access.authenticate') }}">
                @csrf
                <label for="site-access-code">Codice di accesso</label>
                <input
                    id="site-access-code"
                    name="code"
                    type="password"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    required
                    autofocus
                >

                @error('code')
                    <p class="contact-error" role="alert">{{ $message }}</p>
                @enderror

                <button class="btn-2" type="submit">Accedi</button>
            </form>
        </div>
    </main>
</body>

</html>
