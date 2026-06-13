@extends('layouts.app')

@section('title', 'Mima Studio - Homepage')

@section('hero')
    <section class="hero-section">
        <div class="hero-content-wrapper fullheight fit-wrapper">
            <div class="stack-large hero-content">
                <h2 class="light-color">Michele Mariani:<br>fotografia autentica<br>da Lucca al mondo.</h2>
                <a class="btn" href="/portfolio">Guarda il portfolio</a>
            </div>
            <div class="marquee">
                <div class="marquee-track" id="track">
                    <h1 class="light-color hero-title">Michele Mariani -</h1>
                    <h1 class="light-color hero-title">Michele Mariani -</h1>
                    <h1 class="light-color hero-title">Michele Mariani -</h1>
                    <h1 class="light-color hero-title">Michele Mariani -</h1>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('content')
        <section class="section-standard bottom-line">
            <div class="text-container-center center-items center-text stack-large">
                <p class="pill">CHI SONO</p>
                <h2>Michele Mariani è un giovane fotografo lucchese che racconta persone, eventi e brand con uno sguardo pulito, diretto e contemporaneo.</h2>
                <a class="btn-2" href="/contatti">Parliamo del tuo progetto</a>
            </div>
        </section>

        <section class="bottom-line section-small-xl-padding">
            <div class="grid-2">
                <div>
                    <img class="sticky"
                        src="https://assets-global.website-files.com/65f45868d16d48662164da00/65fa099d2c6b098ca488f97d_Image%20037.webp"
                        style="width: 100%; border-radius: 8px;">
                </div>
                <div class="center-vertical">
                    <div class="stack-large top-margin text-container">
                        <div class="pill">LA MIA STORIA</div>
                        <h2>Una fotografia giovane, radicata nel territorio e abituata a contesti importanti.</h2>
                        <p>Dal lavoro in eventi come Lucca Comics and Games alle cerimonie, dagli shooting per brand con visibilità internazionale come sunsetersbrand.com ai ritratti di personalità di spicco del territorio lucchese, Michele costruisce immagini curate, naturali e riconoscibili.</p>
                        <div class="icon-wrapper">
                            <a class="social-icon" href="https://www.instagram.com/michelemariani.fotografie/">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-instagram">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4 8a4 4 0 0 1 4 -4h8a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-8a4 4 0 0 1 -4 -4l0 -8" />
                                    <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                    <path d="M16.5 7.5v.01" />
                                </svg>
                            </a>
                            <a class="social-icon" href="https://www.instagram.com/michelemariani.wedding/">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-camera-heart">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M10.5 20h-5.5a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h1a2 2 0 0 0 2 -2a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1a2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v2" />
                                    <path d="M14.41 11.212a3 3 0 1 0 -4.15 4.231" />
                                    <path
                                        d="M18 22l3.35 -3.284a2.143 2.143 0 0 0 .005 -3.071a2.242 2.242 0 0 0 -3.129 -.006l-.224 .22l-.223 -.22a2.242 2.242 0 0 0 -3.128 -.006a2.143 2.143 0 0 0 -.006 3.071l3.355 3.296" />
                                </svg>
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-standard bottom-line">
            <div class="text-container-center center-items center-text stack-large">
                <p class="pill">PROGETTI SELEZIONATI</p>
                <h2>Eventi, brand, ritratti e cerimonie: una selezione di lavori costruiti con attenzione al racconto.</h2>
                <a class="btn-2" href="/portfolio">Vai al portfolio</a>
            </div>

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

        <section class="section-xl-padding bottom-line">
            <div class="wrapper grid-2">
                <div class="stack-large">
                    <div class="text-container stack-large">
                        <p class="pill">ESPERIENZA</p>
                        <h1>Dalla scena lucchese a progetti capaci di parlare anche fuori dai confini locali.</h1>
                        <h4>Ogni servizio nasce da ascolto, preparazione e attenzione al contesto.</h4>
                    </div>
                    <div class="grid-2 top-margin">
                        <div class="stack-mid">
                            <h2>LC&G</h2>
                            <div class="stack-small">
                                <p><strong>Eventi e cultura pop</strong></p>
                                <p>Esperienza maturata anche in contesti dinamici come Lucca Comics and Games.</p>
                            </div>
                        </div>

                        <div class="stack-mid">
                            <h2>Brand</h2>
                            <div class="stack-small">
                                <p><strong>Progetti commerciali</strong></p>
                                <p>Shooting per realtà italiane e marchi con rilevanza internazionale.</p>
                            </div>
                        </div>

                        <div class="stack-mid">
                            <h2>Wedding</h2>
                            <div class="stack-small">
                                <p><strong>Cerimonie</strong></p>
                                <p>Racconti fotografici discreti, spontanei e attenti ai momenti veri.</p>
                            </div>
                        </div>

                        <div class="stack-mid">
                            <h2>Lucca</h2>
                            <div class="stack-small">
                                <p><strong>Ritratti del territorio</strong></p>
                                <p>Volti, professionisti e personalità che danno forma alla scena lucchese.</p>
                            </div>
                        </div>


                    </div>
                </div>
                <img class="sticky-on-navbar" src="{{ asset('images/portfolio-1.jpeg') }}" style="width: 100%; ">
            </div>
        </section>

        <section class="section-xl-padding">
            <div class="wrapper center-items">
                <div class="text-container-center center-text stack-mid">
                    <p class="pill">SERVIZI</p>
                    <h2>Servizi fotografici pensati per persone, eventi e brand che cercano immagini solide e naturali.</h2>
                </div>
                <div class="top-margin-xxl services-container">
                    <div class="bottom-line"></div>
                    <div class="service bottom-line">
                        <img style="width: 100%; aspect-ratio: 3/2; object-fit: cover;" src="{{ asset('images/portfolio-1.jpeg') }}">
                        <div class="flex-center-start flex-col stack-mid">
                            <h3>Ritratti</h3>
                            <p>Per professionisti, artisti e personalità del territorio.</p>
                        </div>
                        <div class="center-items">
                            <p class="pill">SU RICHIESTA</p>
                        </div>
                        <div class="flex-center-end">
                            <a class="round-pill right-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-move-up-right-icon lucide-move-up-right">
                                    <path d="M13 5H19V11" />
                                    <path d="M19 5L5 19" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="service bottom-line">
                        <img style="width: 100%; aspect-ratio: 3/2; object-fit: cover;" src="{{ asset('images/portfolio-1.jpeg') }}">
                        <div class="flex-center-start flex-col stack-mid">
                            <h3>Cerimonie</h3>
                            <p>Matrimoni, eventi privati e momenti da raccontare con discrezione.</p>
                        </div>
                        <div class="center-items">
                            <p class="pill">SU RICHIESTA</p>
                        </div>
                        <div class="flex-center-end">
                            <a class="round-pill right-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-move-up-right-icon lucide-move-up-right">
                                    <path d="M13 5H19V11" />
                                    <path d="M19 5L5 19" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="service bottom-line">
                        <img style="width: 100%; aspect-ratio: 3/2; object-fit: cover;" src="{{ asset('images/portfolio-1.jpeg') }}">
                        <div class="flex-center-start flex-col stack-mid">
                            <h3>Brand e prodotto</h3>
                            <p>Immagini per campagne, cataloghi e comunicazione digitale.</p>
                        </div>
                        <div class="center-items">
                            <p class="pill">SU RICHIESTA</p>
                        </div>
                        <div class="flex-center-end">
                            <a class="round-pill right-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-move-up-right-icon lucide-move-up-right">
                                    <path d="M13 5H19V11" />
                                    <path d="M19 5L5 19" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="service bottom-line">
                        <img style="width: 100%; aspect-ratio: 3/2; object-fit: cover;" src="{{ asset('images/portfolio-1.jpeg') }}">
                        <div class="flex-center-start flex-col stack-mid">
                            <h3>Eventi</h3>
                            <p>Reportage fotografico per festival, format culturali e occasioni pubbliche.</p>
                        </div>
                        <div class="center-items">
                            <p class="pill">SU RICHIESTA</p>
                        </div>
                        <div class="flex-center-end">
                            <a class="round-pill right-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-move-up-right-icon lucide-move-up-right">
                                    <path d="M13 5H19V11" />
                                    <path d="M19 5L5 19" />
                                </svg>
                            </a>
                        </div>
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
