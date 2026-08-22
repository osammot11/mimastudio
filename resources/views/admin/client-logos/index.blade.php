@extends('layouts.admin')

@section('title', 'Loghi clienti - Mima Studio')
@section('page-title', 'Loghi clienti')
@section('eyebrow', 'Homepage')

@section('actions')
    <a class="admin-btn" href="{{ route('home') }}" target="_blank">Vedi homepage</a>
@endsection

@section('content')
    <div class="admin-logo-page">
        <section class="admin-card admin-form-section">
            <div>
                <h2>Caricamento massivo</h2>
                <p class="admin-meta">Seleziona tutti i loghi da aggiungere allo slider della homepage.</p>
            </div>

            <form class="admin-form" action="{{ route('admin.client-logos.store') }}" method="post"
                enctype="multipart/form-data" data-client-logo-upload>
                @csrf

                <div class="admin-field">
                    <label for="logos">File dei loghi</label>
                    <input id="logos" type="file" name="logos[]" multiple required
                        accept="image/png,image/jpeg,image/webp,.png,.jpg,.jpeg,.webp" data-client-logo-files>
                    <p class="admin-help">JPEG, PNG o WebP, massimo 4 MB ciascuno e 1000 file per selezione. I file vengono caricati in piccoli gruppi per evitare i limiti PHP.</p>
                    <p class="admin-meta" data-client-logo-selection>Nessun file selezionato.</p>
                </div>

                <div class="admin-upload" data-client-logo-progress hidden>
                    <div class="admin-upload-summary">
                        <strong data-client-logo-status>Preparazione caricamento</strong>
                        <span class="admin-meta" data-client-logo-count>0 / 0</span>
                    </div>
                    <div class="admin-upload-track" role="progressbar" aria-label="Avanzamento upload" aria-valuemin="0"
                        aria-valuemax="100" aria-valuenow="0">
                        <span data-client-logo-progress-bar></span>
                    </div>
                    <p class="admin-meta" data-client-logo-detail>0%</p>
                    <p class="admin-error-text" data-client-logo-error hidden></p>
                </div>

                <div>
                    <button class="admin-btn primary" type="submit" data-client-logo-submit>Carica loghi</button>
                </div>
            </form>

            @if ($errors->any())
                <div class="admin-errors">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="admin-card">
            <div class="admin-card-header">
                <div>
                    <h2>Loghi nello slider</h2>
                    <p class="admin-meta">{{ $clientLogos->count() }} elementi totali</p>
                </div>
            </div>

            @if ($clientLogos->isNotEmpty())
                <div class="admin-table admin-logo-table">
                    <div class="admin-table-head">
                        <span>Logo</span>
                        <span>Nome</span>
                        <span>Ordine</span>
                        <span>Sostituisci file</span>
                        <span>Azioni</span>
                    </div>

                    @foreach ($clientLogos as $clientLogo)
                        <form id="delete-client-logo-{{ $clientLogo->id }}"
                            action="{{ route('admin.client-logos.destroy', $clientLogo) }}" method="post" hidden>
                            @csrf
                            @method('DELETE')
                        </form>

                        <form class="admin-row admin-logo-row"
                            action="{{ route('admin.client-logos.update', $clientLogo) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <img class="admin-logo-preview" src="{{ $clientLogo->imageUrl() }}"
                                alt="{{ $clientLogo->name }}">

                            <div class="admin-field">
                                <label class="admin-table-label" for="logo-name-{{ $clientLogo->id }}">Nome</label>
                                <input id="logo-name-{{ $clientLogo->id }}" type="text" name="name"
                                    value="{{ $clientLogo->name }}" required>
                            </div>

                            <div class="admin-field">
                                <label class="admin-table-label" for="logo-order-{{ $clientLogo->id }}">Ordine</label>
                                <input id="logo-order-{{ $clientLogo->id }}" type="number" name="sort_order"
                                    value="{{ $clientLogo->sort_order }}" min="0" required>
                            </div>

                            <div class="admin-field">
                                <label class="admin-table-label" for="logo-file-{{ $clientLogo->id }}">Nuovo file</label>
                                <input id="logo-file-{{ $clientLogo->id }}" type="file" name="logo"
                                    accept="image/png,image/jpeg,image/webp,.png,.jpg,.jpeg,.webp">
                                <p class="admin-help">{{ $clientLogo->original_name }}</p>
                            </div>

                            <div class="admin-actions">
                                <button class="admin-btn" type="submit">Salva</button>
                                <button class="admin-danger" type="submit"
                                    form="delete-client-logo-{{ $clientLogo->id }}"
                                    onclick="return confirm('Eliminare questo logo?')">Elimina</button>
                            </div>
                        </form>
                    @endforeach
                </div>
            @else
                <div class="admin-empty">Nessun logo caricato. Lo slider resterà nascosto finché la lista è vuota.</div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-client-logos.js') }}?v={{ filemtime(public_path('js/admin-client-logos.js')) }}" defer></script>
@endpush
