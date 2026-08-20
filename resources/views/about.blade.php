@extends('layouts.app')

@section('title', 'Chi sono - Michele Mariani Fotografo')

@section('hero')
    <section class="hero-section">
        <div class="hero-content-wrapper fullheight-60 fit-wrapper center-items just-cont-end"
            style="background-position: center 28%;">
            <div class="marquee">
                <div class="marquee-track" id="track">
                    <h1 class="light-color hero-title">Chi sono -</h1>
                    <h1 class="light-color hero-title">Chi sono -</h1>
                    <h1 class="light-color hero-title">Chi sono -</h1>
                    <h1 class="light-color hero-title">Chi sono -</h1>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="section-standard bottom-line">
        <div class="wrapper grid-2">
            <div class="stack-large text-container">
                <p class="pill">MICHELE MARIANI</p>
                <h1>Fotografo lucchese, con uno sguardo giovane e contemporaneo.</h1>
            </div>
            <div class="center-vertical stack-large text-container">
                <p>Racconto persone, eventi e brand attraverso immagini autentiche, curate e capaci di restituire il carattere di ciò che ho davanti.</p>
                <div>
                    <a class="btn-2" href="{{ route('contatti') }}">Parliamo del tuo progetto</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-small-xl-padding bottom-line">
        <div class="wrapper grid-2">
            <div>
                <img class="sticky" src="{{ asset('images/foto-miche.jpg') }}" alt="Michele Mariani fotografo"
                    style="display: block; width: 100%; max-height: 820px; object-fit: cover; border-radius: 8px;">
            </div>

            <div class="center-vertical">
                <div class="stack-large text-container">
                    <p class="pill">LA MIA STORIA</p>
                    <h2>La fotografia come modo di osservare, incontrare e raccontare.</h2>
                    <p>Sono cresciuto a Lucca e qui ho iniziato a costruire il mio percorso, lavorando in situazioni molto diverse tra loro: dai ritratti alle cerimonie, dagli eventi ai progetti commerciali.</p>
                    <p>Ho maturato esperienza in contesti importanti come Lucca Comics & Games, dove velocità, attenzione e capacità di leggere ciò che accade sono fondamentali. Ho inoltre realizzato shooting per brand con rilevanza internazionale, tra cui Sunseters, e ritratto personalità di spicco del territorio lucchese.</p>
                    <p>Ogni incarico è diverso, ma il mio obiettivo rimane lo stesso: creare immagini solide e naturali, senza forzature, che abbiano senso oggi e continuino ad averlo nel tempo.</p>
                    <div class="icon-wrapper">
                        <a class="social-icon" href="https://www.instagram.com/michelemariani.fotografie/" target="_blank"
                            rel="noopener noreferrer" aria-label="Instagram Michele Mariani Fotografie">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect width="16" height="16" x="4" y="4" rx="4" />
                                <circle cx="12" cy="12" r="3" />
                                <path d="M16.5 7.5h.01" />
                            </svg>
                        </a>
                        <a class="social-icon" href="https://www.instagram.com/michelemariani.wedding/" target="_blank"
                            rel="noopener noreferrer" aria-label="Instagram Michele Mariani Wedding">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M14.5 4h-5L7.7 7H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-2.7z" />
                                <circle cx="12" cy="13" r="3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-standard bottom-line">
        <div class="text-container-center center-items center-text stack-large">
            <p class="pill">IL MIO APPROCCIO</p>
            <h2>Presente quando serve, discreto quando conta.</h2>
            <p>Un buon risultato nasce prima dello scatto: dall'ascolto, dalla preparazione e dalla fiducia costruita con chi ho davanti.</p>
        </div>

        <div class="wrapper grid-3 top-margin-xxl">
            <div class="stack-mid">
                <p class="pill">01</p>
                <h3>Ascolto</h3>
                <p>Parto dalle persone, dal contesto e dall'obiettivo del progetto per definire un racconto visivo coerente.</p>
            </div>
            <div class="stack-mid">
                <p class="pill">02</p>
                <h3>Presenza</h3>
                <p>Lavoro con attenzione e discrezione, seguendo ciò che accade senza interromperne la spontaneità.</p>
            </div>
            <div class="stack-mid">
                <p class="pill">03</p>
                <h3>Cura</h3>
                <p>Dalla selezione alla post-produzione, ogni immagine viene trattata per mantenere un risultato pulito e riconoscibile.</p>
            </div>
        </div>
    </section>

    <section class="section-xl-padding bottom-line">
        <div class="wrapper grid-2">
            <div class="center-vertical">
                <div class="stack-large text-container">
                    <p class="pill">ESPERIENZA</p>
                    <h2>Dal territorio lucchese a progetti capaci di viaggiare.</h2>
                    <p>Ritratti, matrimoni, grandi eventi e shooting per brand richiedono linguaggi differenti. Muovermi tra questi mondi mi ha insegnato ad adattare il metodo senza perdere identità e qualità.</p>
                    <a class="btn-2" href="{{ route('portfolio') }}">Scopri i miei lavori</a>
                </div>
            </div>
            <img src="{{ asset('images/portfolio-5.jpeg') }}" alt="Un progetto fotografico di Michele Mariani"
                style="display: block; width: 100%; min-height: 480px; object-fit: cover; border-radius: 8px;">
        </div>
    </section>

    <section class="section-standard">
        <div class="text-container-center center-items center-text stack-large">
            <p class="pill">LAVORIAMO INSIEME</p>
            <h2>Hai una persona, un evento o un progetto da raccontare?</h2>
            <a class="btn-2" href="{{ route('contatti') }}">Scrivi a Michele</a>
        </div>
    </section>
@endsection
