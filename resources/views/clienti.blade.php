@extends('layouts.app')

@section('title', 'Clienti - Mima Studio')

@section('hero')
    <section class="hero-section">
        <div class="hero-content-wrapper fullheight-60 fit-wrapper center-items just-cont-end">
            <div class="marquee">
                <div class="marquee-track" id="track">
                    <h1 class="light-color hero-title">Clienti -</h1>
                    <h1 class="light-color hero-title">Clienti -</h1>
                    <h1 class="light-color hero-title">Clienti -</h1>
                    <h1 class="light-color hero-title">Clienti -</h1>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="section-standard bottom-line">
        <div class="text-container-center center-items center-text stack-large">
            <p class="pill">CLIENTI</p>
            <h2>Collaborazioni, brand e realtà del territorio raccontate attraverso immagini curate e riconoscibili.</h2>
        </div>

        <div class="grid-2 top-margin small-gap">
            @forelse ($clients as $client)
                <a class="portfolio-card" href="{{ route('clienti.show', $client) }}" style="background-image: url('{{ $client->coverImageUrl() }}');">
                    <div class="portfolio-content display-none">
                        <h3>{{ $client->name }}</h3>
                        <h6>{{ $client->description }}</h6>
                    </div>
                </a>
            @empty
                <div class="text-container stack-mid">
                    <h3>Nessun cliente pubblicato.</h3>
                    <p>Torna presto per vedere le nuove collaborazioni.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
