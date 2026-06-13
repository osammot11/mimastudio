@extends('layouts.app')

@section('title', 'Contatti - Mima Studio')

@section('content')
    <section class="section-standard bottom-line">
        <div class="wrapper grid-2">
            <div class="stack-large text-container">
                <p class="pill">CONTATTI</p>
                <h1>Parliamo del tuo prossimo servizio fotografico.</h1>
            </div>
            <div class="center-vertical">
                <p>Raccontami cosa hai in mente: un ritratto, un matrimonio, un evento o un progetto editoriale. Ti risponderò con disponibilità, tempi e prossimi passi.</p>
            </div>
        </div>
    </section>

    <section class="section-xl-padding">
        <div class="wrapper grid-2">
            <div class="stack-xl">
                <div class="stack-mid bottom-line">
                    <p class="pill">EMAIL</p>
                    <h3>info@mimastudio.it</h3>
                </div>

                <div class="stack-mid bottom-line">
                    <p class="pill">TELEFONO</p>
                    <h3>+39 388 866 1486</h3>
                </div>

                <div class="stack-mid">
                    <p class="pill">SOCIAL</p>
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

            <form class="contact-form stack-mid" action="mailto:hello@mimastudio.it" method="post" enctype="text/plain">
                <div class="grid-2 mid-gap">
                    <input type="text" name="name" placeholder="Nome" autocomplete="name">
                    <input type="email" name="email" placeholder="Email" autocomplete="email">
                </div>
                <input type="text" name="subject" placeholder="Tipo di servizio">
                <textarea name="message" rows="7" placeholder="Raccontami il progetto"></textarea>
                <button class="btn-2" type="submit">Invia richiesta</button>
            </form>
        </div>
    </section>
@endsection
