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

            <form id="contact-form" class="contact-form contact-wizard" action="{{ route('contatti.store') }}" method="post">
                @csrf

                @if (session('contact_success'))
                    <div class="contact-success" role="status">
                        <p class="pill">RICHIESTA RICEVUTA</p>
                        <h4>{{ session('contact_success') }}</h4>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="contact-server-errors" role="alert">
                        <p>Non è stato possibile inviare la richiesta. Controlla i campi e riprova.</p>
                    </div>
                @endif

                <div class="contact-progress" aria-live="polite">
                    <p class="pill">PASSAGGIO <span data-step-current>1</span> DI <span data-step-total>3</span></p>
                    <div class="contact-progress-line" aria-hidden="true">
                        <span></span>
                    </div>
                </div>

                <div class="contact-steps">
                    <fieldset class="contact-step stack-large" data-contact-step>
                        <div class="stack-small">
                            <p class="pill">INIZIAMO</p>
                            <h3>Come posso ricontattarti?</h3>
                        </div>
                        <div class="grid-2 mid-gap">
                            <label>
                                <span>Nome completo</span>
                                <input type="text" name="nome_completo" value="{{ old('nome_completo') }}" autocomplete="name" required>
                            </label>
                            <label>
                                <span>Email</span>
                                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
                            </label>
                        </div>
                        <label>
                            <span>Telefono</span>
                            <input type="tel" name="telefono" value="{{ old('telefono') }}" autocomplete="tel" required>
                        </label>
                    </fieldset>

                    <fieldset class="contact-step stack-large" data-contact-step>
                        <div class="stack-small">
                            <p class="pill">IL PROGETTO</p>
                            <h3>Che tipo di servizio stai cercando?</h3>
                        </div>
                        <div class="contact-options">
                            @foreach (['ritratti', 'cerimonie', 'matrimoni', 'brand', 'grandi eventi', 'eventi aziendali', 'altro'] as $projectType)
                                <label class="contact-option">
                                    <input type="radio" name="tipo_progetto" value="{{ $projectType }}"
                                        @checked(old('tipo_progetto') === $projectType) required>
                                    <span>{{ ucfirst($projectType) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <fieldset class="contact-step stack-large" data-contact-step data-wedding-step disabled>
                        <div class="stack-small">
                            <p class="pill">IL GIORNO</p>
                            <h3>Quando e come vi sposerete?</h3>
                        </div>
                        <div class="grid-2 mid-gap">
                            <label>
                                <span>Data delle nozze</span>
                                <input type="date" name="data_nozze" value="{{ old('data_nozze') }}" required>
                            </label>
                            <label>
                                <span>Orario delle nozze <small>(facoltativo)</small></span>
                                <input type="time" name="orario_nozze" value="{{ old('orario_nozze') }}">
                            </label>
                        </div>
                        <div class="contact-options">
                            <label class="contact-option">
                                <input type="radio" name="tipo_cerimonia" value="religiosa"
                                    @checked(old('tipo_cerimonia') === 'religiosa') required>
                                <span>Cerimonia religiosa</span>
                            </label>
                            <label class="contact-option">
                                <input type="radio" name="tipo_cerimonia" value="civile"
                                    @checked(old('tipo_cerimonia') === 'civile') required>
                                <span>Cerimonia civile</span>
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="contact-step stack-large" data-contact-step data-wedding-step disabled>
                        <div class="stack-small">
                            <p class="pill">IL RICEVIMENTO</p>
                            <h3>Raccontatemi qualcosa della giornata.</h3>
                        </div>
                        <label>
                            <span>Location del ricevimento</span>
                            <input type="text" name="location_ricevimento" value="{{ old('location_ricevimento') }}" required>
                        </label>
                        <div class="grid-2 mid-gap">
                            <label>
                                <span>Numero approssimativo degli invitati</span>
                                <input type="number" name="numero_invitati" value="{{ old('numero_invitati') }}" min="1"
                                    inputmode="numeric" required>
                            </label>
                            <label>
                                <span>Tipo di richiesta</span>
                                <select name="tipo_richiesta" required>
                                    <option value="">Seleziona</option>
                                    <option value="foto" @selected(old('tipo_richiesta') === 'foto')>Foto</option>
                                    <option value="foto e video" @selected(old('tipo_richiesta') === 'foto e video')>Foto + video</option>
                                </select>
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="contact-step stack-large" data-contact-step data-wedding-step disabled>
                        <div class="stack-small">
                            <p class="pill">LA COPERTURA</p>
                            <h3>Quali momenti desiderate includere?</h3>
                            <p>Potete selezionare più opzioni.</p>
                        </div>
                        <div class="contact-options">
                            @foreach ([
                                'Preparazione sposa',
                                'Preparazione sposo',
                                'Festeggiamenti e balli (post taglio della torta)',
                                'Solo cerimonia, posato e rinfresco (fino al taglio della torta, balli esclusi)',
                            ] as $service)
                                <label class="contact-option">
                                    <input type="checkbox" name="servizi_aggiuntivi[]" value="{{ $service }}"
                                        @checked(in_array($service, old('servizi_aggiuntivi', []), true))>
                                    <span>{{ $service }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <fieldset class="contact-step stack-large" data-contact-step data-wedding-step disabled>
                        <div class="stack-small">
                            <p class="pill">SERVIZI PREMIUM</p>
                            <h3>Volete aggiungere qualcosa di speciale?</h3>
                            <p>Potete selezionare più opzioni oppure proseguire senza sceglierne.</p>
                        </div>
                        <div class="contact-options contact-options-list">
                            @foreach ([
                                'Bomboniere fotografiche|Per stupire i vostri invitati regalando loro una foto a fine giornata.',
                                'Servizio Polaroid|Per gli amanti delle macchine fotografiche istantanee.',
                                'Stampe via WhatsApp|Per divertirvi a essere voi i reporter del vostro giorno.',
                                'Servizio prematrimoniale|Un ricordo degli ultimi giorni da fidanzati.',
                                'Servizio post-matrimoniale|Un ricordo dei primi giorni da sposi in una location italiana.',
                            ] as $premiumService)
                                @php([$premiumTitle, $premiumDescription] = explode('|', $premiumService))
                                <label class="contact-option">
                                    <input type="checkbox" name="servizi_premium[]" value="{{ $premiumTitle }}"
                                        @checked(in_array($premiumTitle, old('servizi_premium', []), true))>
                                    <span><strong>{{ $premiumTitle }}</strong><small>{{ $premiumDescription }}</small></span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <fieldset class="contact-step stack-large" data-contact-step data-wedding-step disabled>
                        <div class="stack-small">
                            <p class="pill">LA VOSTRA STORIA</p>
                            <h3>Come immaginate il vostro matrimonio?</h3>
                        </div>
                        <label>
                            <span>Raccontateci qualcosa di voi e del matrimonio che sognate</span>
                            <textarea name="racconto_matrimonio" rows="6" required>{{ old('racconto_matrimonio') }}</textarea>
                        </label>
                        <label>
                            <span>Come ci avete conosciuto?</span>
                            <select name="come_conosciuti" required>
                                <option value="">Seleziona</option>
                                @foreach (['Instagram', 'Facebook', 'Google', 'Matrimonio.com', 'Passaparola', 'Evento o fiera', 'Altro'] as $source)
                                    <option value="{{ $source }}" @selected(old('come_conosciuti') === $source)>{{ $source }}</option>
                                @endforeach
                            </select>
                        </label>
                    </fieldset>

                    <fieldset class="contact-step stack-large" data-contact-step>
                        <div class="stack-small">
                            <p class="pill">ULTIMO PASSAGGIO</p>
                            <h3>C'è altro che dovrei sapere?</h3>
                        </div>
                        <label>
                            <span>Messaggio</span>
                            <textarea name="messaggio" rows="7" required>{{ old('messaggio') }}</textarea>
                        </label>
                        <label class="contact-privacy">
                            <input type="checkbox" name="privacy" value="1" @checked(old('privacy')) required>
                            <span>Ho letto la <a href="{{ route('privacy-policy') }}" target="_blank">Privacy Policy</a> e
                                acconsento al trattamento dei dati personali.</span>
                        </label>
                    </fieldset>
                </div>

                <p class="contact-error" role="alert" aria-live="assertive"></p>

                <div class="contact-navigation">
                    <button class="btn-2" type="button" data-contact-prev>Indietro</button>
                    <button class="btn-2" type="button" data-contact-next>Continua</button>
                    <button class="btn-2" type="submit" data-contact-submit>Invia richiesta</button>
                </div>
            </form>
        </div>
    </section>
@endsection
