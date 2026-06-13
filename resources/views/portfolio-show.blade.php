@extends('layouts.app')

@section('title', $project->title . ' - Mima Studio')

@section('hero')
    <section class="hero-section">
        <div class="hero-content-wrapper fullheight-60 fit-wrapper center-items just-cont-end" style="background-image: linear-gradient(rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.35)), url('{{ $project->coverImageUrl() }}');">
            <div class="marquee">
                <div class="marquee-track" id="track">
                    <h1 class="light-color hero-title">{{ $project->title }} -</h1>
                    <h1 class="light-color hero-title">{{ $project->title }} -</h1>
                    <h1 class="light-color hero-title">{{ $project->title }} -</h1>
                    <h1 class="light-color hero-title">{{ $project->title }} -</h1>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="section-standard bottom-line">
        <div class="wrapper grid-2">
            <div class="stack-large text-container">
                @if ($project->category)
                    <p class="pill">{{ strtoupper($project->category) }}</p>
                @endif
                <h1>{{ $project->title }}</h1>
            </div>
            <div class="center-vertical">
                <div class="stack-mid">
                    <p>{{ $project->description }}</p>
                    @if ($project->body)
                        <p>{!! nl2br(e($project->body)) !!}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="section-xl-padding">
        <div class="wrapper lightbox-gallery adaptive-gallery">
            <img src="{{ $project->coverImageUrl() }}" alt="{{ $project->title }}">

            @foreach ($project->images as $image)
                <img src="{{ $image->imageUrl() }}" alt="{{ $image->alt_text ?: $project->title }}">
            @endforeach
        </div>
    </section>
@endsection
