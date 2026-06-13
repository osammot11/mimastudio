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
                </div>
            </div>
        </div>
    </section>

    <section class="section-xl-padding">
        <div class="wrapper lightbox-gallery adaptive-gallery">
            <img src="{{ $client->photoImageUrl() }}" alt="{{ $client->name }}">
            <img src="{{ $client->coverImageUrl() }}" alt="{{ $client->name }}">

            @foreach ($client->images as $image)
                <img src="{{ $image->imageUrl() }}" alt="{{ $image->alt_text ?: $client->name }}">
            @endforeach
        </div>
    </section>
@endsection
