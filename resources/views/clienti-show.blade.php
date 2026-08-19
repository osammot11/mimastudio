@extends('layouts.app')

@section('title', $client->name . ' - Mima Studio')

@section('hero')
    <section class="hero-section">
        <div class="hero-content-wrapper fullheight-60 fit-wrapper center-items just-cont-end" style="background-image: linear-gradient(rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.35)), url('{{ $client->coverImageUrl() }}');">
            <div class="marquee">
                <div class="marquee-track" id="track">
                    <h1 class="light-color hero-title">{{ $client->name }} -</h1>
                    <h1 class="light-color hero-title">{{ $client->name }} -</h1>
                    <h1 class="light-color hero-title">{{ $client->name }} -</h1>
                    <h1 class="light-color hero-title">{{ $client->name }} -</h1>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="section-standard bottom-line">
        <div class="wrapper grid-2">
            <div class="stack-large text-container">
                <p class="pill">CLIENTE</p>
                <h1>{{ $client->name }}</h1>
            </div>
            <div class="center-vertical">
                <div class="stack-mid">
                    @if ($client->client_date)
                        <p>{{ $client->client_date->format('d/m/Y') }}</p>
                    @endif
                    <p>{{ $client->description }}</p>
                    @if ($client->video_url)
                        <a class="btn-2" href="{{ $client->video_url }}" target="_blank" rel="noopener noreferrer">
                            Guarda o scarica il video
                        </a>
                    @endif
                    @if ($client->high_resolution_url)
                        <a class="btn-2" href="{{ $client->high_resolution_url }}" target="_blank" rel="noopener noreferrer">
                            Scarica il lavoro in alta risoluzione
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="section-xl-padding">
        <div class="wrapper lightbox-gallery adaptive-gallery" data-progressive-gallery
            data-gallery-endpoint="{{ route('clienti.gallery', $client) }}"
            data-next-cursor="{{ $galleryImages->nextCursor()?->encode() }}">
            <img src="{{ $client->photoImageUrl() }}" data-full-src="{{ $client->photoImageUrl() }}" alt="{{ $client->name }}" decoding="async">
            <img src="{{ $client->coverImageUrl() }}" data-full-src="{{ $client->coverImageUrl() }}" alt="{{ $client->name }}" decoding="async">

            @foreach ($galleryImages as $image)
                <img src="{{ $image->thumbnailUrl() }}" data-full-src="{{ $image->imageUrl() }}"
                    data-width="{{ $image->width }}" data-height="{{ $image->height }}"
                    alt="{{ $image->alt_text ?: $client->name }}" loading="lazy" decoding="async">
            @endforeach
        </div>
        <div class="gallery-load-sentinel" data-gallery-sentinel @if (! $galleryImages->hasMorePages()) hidden @endif>
            Caricamento immagini
        </div>
    </section>
@endsection
