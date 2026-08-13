@extends('layouts.admin')

@section('title', 'Link area clienti - Mima Studio')
@section('page-title', 'Link area clienti')
@section('eyebrow', 'Accessi riservati')

@section('actions')
    <a class="admin-btn" href="{{ route('admin.customers.index') }}">Apri anagrafica</a>
@endsection

@section('content')
    <section class="admin-card">
        <div class="admin-card-header">
            <div>
                <h2>Accessi al portale</h2>
                <p class="admin-meta">
                    I link disponibili restano validi fino al {{ $expiresAt->format('d/m/Y H:i') }}.
                </p>
            </div>
        </div>

        @if ($customers->isNotEmpty())
            <div class="admin-table admin-portal-links">
                <div class="admin-table-head">
                    <span>Cliente</span>
                    <span>Contenuti</span>
                    <span>Link di accesso</span>
                    <span>Azione</span>
                </div>

                @foreach ($customers as $customer)
                    <div class="admin-row">
                        <div class="admin-title">
                            <h3>{{ $customer->name }}</h3>
                            <p class="admin-meta">{{ $customer->email ?: 'Email non disponibile' }}</p>
                        </div>

                        <span>
                            {{ $customer->portal_content_count }}
                            {{ $customer->portal_content_count === 1 ? 'contenuto' : 'contenuti' }}
                        </span>

                        @if ($customer->portal_access_url)
                            <div class="admin-field">
                                <input id="portal-link-{{ $customer->id }}" type="text"
                                    value="{{ $customer->portal_access_url }}" readonly
                                    aria-label="Link di accesso di {{ $customer->name }}">
                            </div>
                            <button class="admin-btn" type="button"
                                data-copy-link="portal-link-{{ $customer->id }}">
                                Copia link
                            </button>
                        @else
                            <span class="admin-help">
                                @if (! $customer->email)
                                    Inserisci un'email nell'anagrafica del cliente.
                                @else
                                    Nessun contenuto visibile nell'area riservata.
                                @endif
                            </span>
                            <span class="admin-status is-hidden">Non disponibile</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="admin-empty">Nessun cliente registrato.</div>
        @endif
    </section>

    <script>
        (() => {
            document.querySelectorAll('[data-copy-link]').forEach(button => {
                button.addEventListener('click', async () => {
                    const input = document.getElementById(button.dataset.copyLink);

                    if (!input) {
                        return;
                    }

                    try {
                        await navigator.clipboard.writeText(input.value);
                    } catch {
                        input.focus();
                        input.select();
                        document.execCommand('copy');
                    }

                    const originalText = button.textContent;
                    button.textContent = 'Copiato';

                    window.setTimeout(() => {
                        button.textContent = originalText;
                    }, 1800);
                });
            });
        })();
    </script>
@endsection
