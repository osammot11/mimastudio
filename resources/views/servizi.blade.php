@extends('layouts.app')

@section('title', 'Mima Studio - Servizi')

@section('hero')
    <section class="hero-section">
        <div class="hero-content-wrapper fullheight-60 fit-wrapper center-items just-cont-end">
            <div class="marquee">
                <div class="marquee-track" id="track">
                    <h1 class="light-color hero-title">Servizi -</h1>
                    <h1 class="light-color hero-title">Servizi -</h1>
                    <h1 class="light-color hero-title">Servizi -</h1>
                    <h1 class="light-color hero-title">Servizi -</h1>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
        <section class="section-standard bottom-line">
            <div class="text-container-center center-items center-text stack-large">
                <p class="pill">SERVIZI</p>
                <h2>Scegli il tipo di racconto fotografico più vicino al tuo progetto.</h2>
            </div>

            @if ($categories->isNotEmpty())
                <div class="filter-pills top-margin-mid">
                    <a @class(['filter-pill', 'active' => ! $activeCategory]) href="{{ route('servizi') }}">Tutti</a>
                    @foreach ($categories as $category)
                        <a
                            @class(['filter-pill', 'active' => $activeCategory === $category])
                            href="{{ route('servizi', ['categoria' => \Illuminate\Support\Str::slug($category)]) }}"
                        >
                            {{ $category }}
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        @foreach ($services as $service)
            <section class="bottom-line section-small-xl-padding">
                <div class="grid-2">
                    @if ($loop->iteration % 2 === 1)
                        <div>
                            <img class="sticky" src="{{ $service['image'] }}" style="width: 100%; border-radius: 8px;" alt="{{ $service['category'] }}">
                        </div>
                    @endif

                    <div class="center-vertical">
                        <div class="stack-large top-margin text-container">
                            <div class="pill">{{ strtoupper($service['category']) }}</div>
                            <h2>{{ $service['title'] }}</h2>
                            <p>{{ $service['description'] }}</p>
                            <div class="stack-large">
                                @foreach ($service['steps'] as $step)
                                    <div class="dot-line-box"></div>
                                    <p>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}  |  {{ $step }}</p>
                                @endforeach
                                <div class="dot-line-box"></div>
                            </div>
                        </div>
                    </div>

                    @if ($loop->iteration % 2 === 0)
                        <div>
                            <img class="sticky" src="{{ $service['image'] }}" style="width: 100%; border-radius: 8px;" alt="{{ $service['category'] }}">
                        </div>
                    @endif
                </div>
            </section>
        @endforeach

        <section class="section-xl-padding">
            <div class="wrapper center-items">
                <div class="text-container-center center-text stack-mid">
                    <p class="pill">FAQ</p>
                    <h2>Domande frequenti prima di iniziare un servizio fotografico.</h2>
                </div>
                <div id="faq-block" class="stack-large top-margin services-container">
                        <div class="dot-line-box"></div>
                        <div class="faq-question">
                            <h4 class="grey">01</h4>
                            <h4>Quanto tempo prima conviene prenotare un servizio?</h4>
                            <div class="center-items-end">
                              <div class="round-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                              </div>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Per ritratti e shooting brand è utile muoversi con almeno due o tre settimane di anticipo. Per cerimonie ed eventi, soprattutto nei periodi più richiesti, è meglio bloccare la data appena possibile.</p>
                        </div>

                        <div class="dot-line-box"></div>
                        <div class="faq-question">
                            <h4 class="grey">02</h4>
                            <h4>Cosa include un servizio fotografico?</h4>
                            <div class="center-items-end">
                              <div class="round-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                              </div>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Ogni servizio include un confronto iniziale, lo shooting, la selezione degli scatti, la post-produzione e la consegna digitale delle immagini finali. Il numero di foto e le tempistiche vengono definiti in base al progetto.</p>
                        </div>

                        <div class="dot-line-box"></div>
                        <div class="faq-question">
                            <h4 class="grey">03</h4>
                            <h4>Realizzi anche stampe, album o consegne personalizzate?</h4>
                            <div class="center-items-end">
                              <div class="round-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus-icon lucide-plus"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                              </div>
                            </div>
                        </div>
                        <div class="faq-answer">
                            <p>Sì, quando il progetto lo richiede è possibile prevedere stampe, album o formati specifici per comunicazione e archiviazione. La consegna viene concordata prima dello shooting.</p>
                        </div>
                        
                </div>
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
