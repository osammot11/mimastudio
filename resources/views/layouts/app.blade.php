<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'Mima Studio')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=2.3">
</head>

<body>
    @yield('hero')

    <div class="sticky-wrapper">
        <section class="navbar grid-3-flex bottom-line">
            <div class="navbar-element navbar-left mobile-none">
                <a href="/portfolio">Portfolio</a>
                <a href="/clienti">Clienti</a>
                <a href="/about">Chi sono</a>
            </div>
            <div class="navbar-element center-items">
                <a href="/">
                    <h5>Michele Mariani</h5>
                </a>
            </div>
            <div class="desktop-none center-items navbar-right">
                <svg id="menu-button" class="navbar-menu-button icon icon-tabler icons-tabler-outline icon-tabler-menu"
                    role="button" aria-controls="mobile-navbar-content"
                    aria-expanded="false" tabindex="0" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 8l16 0" />
                    <path d="M4 16l16 0" />
                </svg>
            </div>
            <div class="navbar-element navbar-right mobile-none">
                <a href="/servizi">Servizi</a>
                <a href="/contatti">Contatti</a>
            </div>

            <div id="mobile-navbar-content" class="navbar-open navbar-closed">
                <a href="/portfolio">
                    <h3>Portfolio</h3>
                </a>
                <a href="/clienti">
                    <h3>Clienti</h3>
                </a>
                <a href="/about">
                    <h3>Chi sono</h3>
                </a>
                <a href="/servizi">
                    <h3>Servizi</h3>
                </a>
                <a href="/contatti">
                    <h3>Contatti</h3>
                </a>
            </div>
        </section>

        <main>
            @yield('content')
        </main>

        <footer class="fullheight">
            <section class="footer-section">
                <div class="grid-2 footer-columns-wrapper">
                    <div class="stack-mid mobile-center">
                        <h4>Hai un progetto, un evento o una storia da raccontare?</h4>
                        <a class="btn-2" href="/contatti">Scrivi a Michele</a>
                    </div>
                    <div class="grid-3 mobile-gap-large">
                        <div class="footer-column stack-mid mobile-center">
                            <h6>Il sito</h6>
                            <a href="/portfolio">Portfolio</a>
                            <a href="/clienti">Clienti</a>
                            <a href="/servizi">Servizi</a>
                            <a href="/about">Chi sono</a>
                        </div>
                        <div class="footer-column stack-mid mobile-center">
                            <h6>Policy</h6>
                            <a href="/privacy-policy">Privacy Policy</a>
                            <a href="/cookie-policy">Cookie Policy</a>
                            <a>Le tue preferenze</a>
                            <a>Termini e condizioni</a>
                        </div>
                        <div class="footer-column stack-mid mobile-center">
                            <h6>Social</h6>
                            <a>Instagram</a>
                            <a>Facebook</a>
                            <a>Matrimonio.com</a>
                        </div>
                    </div>
                </div>
                <div class="bottom-line"></div>
                <div class="grid-2 top-margin-mid">
                    <p class="mobile-center">Michele Mariani Fotografo - tutti i diritti riservati</p>
                    <p class="right-text mobile-center">Realizzato da <a href="https://produceavalue.com">Produce a Value</a></p>
                </div>
            </section>
        </footer>
    </div>

    <div class="gallery-lightbox" aria-hidden="true">
        <button class="gallery-lightbox-close" type="button" aria-label="Chiudi immagine">
            <span></span>
            <span></span>
        </button>
        <button class="gallery-lightbox-arrow gallery-lightbox-prev" type="button" aria-label="Immagine precedente">
            <span></span>
        </button>
        <div class="gallery-lightbox-image-wrapper">
            <img src="" alt="">
        </div>
        <button class="gallery-lightbox-arrow gallery-lightbox-next" type="button" aria-label="Immagine successiva">
            <span></span>
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js"></script>
    <script src="{{ asset('js/app.js') }}?v=2.0"></script>
</body>

</html>
