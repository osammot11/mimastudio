@extends('layouts.app')

@section('title', 'Mima Studio - Portfolio')

@section('hero')
    <section class="hero-section">
        <div class="hero-content-wrapper fullheight-60 fit-wrapper center-items just-cont-end">
            <div class="marquee">
                <div class="marquee-track" id="track">
                    <h1 class="light-color hero-title">Portfolio -</h1>
                    <h1 class="light-color hero-title">Portfolio -</h1>
                    <h1 class="light-color hero-title">Portfolio -</h1>
                    <h1 class="light-color hero-title">Portfolio -</h1>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')

        <section class="section-standard bottom-line">
            <div class="text-container-center center-items center-text stack-large">
                <p class="pill">PROGETTI SELEZIONATI</p>
                <h2>Una raccolta di ritratti, eventi, cerimonie e progetti brand realizzati con uno sguardo lucchese e contemporaneo.</h2>
                <a class="btn-2" href="/contatti">Richiedi un servizio</a>
            </div>

            @if ($categories->isNotEmpty())
                <div class="filter-pills top-margin-mid">
                    <a @class(['filter-pill', 'active' => ! $activeCategory]) href="{{ route('portfolio') }}">Tutti</a>
                    @foreach ($categories as $category)
                        <a
                            @class(['filter-pill', 'active' => $activeCategory === $category])
                            href="{{ route('portfolio', ['categoria' => \Illuminate\Support\Str::slug($category)]) }}"
                        >
                            {{ $category }}
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="grid-2 top-margin small-gap">
                @forelse ($projects as $project)
                    <a class="portfolio-card" href="{{ route('portfolio.show', $project) }}" style="background-image: url('{{ $project->coverImageUrl() }}');">
                        <div class="portfolio-content display-none">
                            <h3>{{ $project->title }}</h3>
                            <h6>{{ $project->description }}</h6>
                        </div>
                    </a>
                @empty
                    <div class="text-container stack-mid">
                        <h3>Nessun progetto pubblicato.</h3>
                        <p>Torna presto per vedere i nuovi lavori.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="section-standard fullheight light-color geometric-bg center-items just-cont-end">
            <div class="wrapper">
                <div class="review-container">
                    <div class="review-arrow-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevron-left-icon lucide-chevron-left">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </div>
                    <div class="review-cards-container">
                        <div class="review-track">
                            <div class="review-card stack-xl">
                                <h4>"Michele ha saputo metterci a nostro agio e restituire immagini naturali, curate e mai forzate."</h4>
                                <div class="review-title">
                                    <img src="{{ asset('images/portfolio-1.jpeg') }}" style="width: 100%;">
                                    <div>
                                        <h6>Cliente ritratto</h6>
                                        <p>Ritratti professionali</p>
                                    </div>
                                </div>
                            </div>

                            <div class="review-card stack-xl">
                                <h4>"Ha capito subito il carattere del luogo e lo ha trasformato in una serie di immagini perfette per la nostra comunicazione."</h4>
                                <div class="review-title">
                                    <img src="{{ asset('images/portfolio-2.jpeg') }}" style="width: 100%;">
                                    <div>
                                        <h6>Luca Moretti</h6>
                                        <p>Spazi e location</p>
                                    </div>
                                </div>
                            </div>

                            <div class="review-card stack-xl">
                                <h4>"Le foto prodotto sono finalmente pulite, credibili e riconoscibili. Il processo è stato semplice dall'inizio alla consegna."</h4>
                                <div class="review-title">
                                    <img src="{{ asset('images/portfolio-3.jpeg') }}" style="width: 100%;">
                                    <div>
                                        <h6>Giulia Ferri</h6>
                                        <p>Brand e prodotto</p>
                                    </div>
                                </div>
                            </div>

                            <div class="review-card stack-xl">
                                <h4>"Durante la cerimonia è stato presente senza essere invadente. La gallery finale racconta davvero l'atmosfera di quel giorno."</h4>
                                <div class="review-title">
                                    <img src="{{ asset('images/portfolio-4.jpeg') }}" style="width: 100%;">
                                    <div>
                                        <h6>Marco Rinaldi</h6>
                                        <p>Cerimonia privata</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="review-arrow-right">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevron-right-icon lucide-chevron-right">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>
@endsection
