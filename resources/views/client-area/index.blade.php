@extends('layouts.app')

@section('title', 'I tuoi lavori - Michele Mariani')

@section('content')
    <section class="section-standard bottom-line">
        <div class="wrapper client-portal-heading">
            <div class="stack-large text-container">
                <p class="pill">AREA PRIVATA</p>
                <h1>I tuoi lavori.</h1>
                <p>Qui trovi tutte le gallerie e le consegne associate a {{ $email }}.</p>
            </div>

            <form action="{{ route('client-area.logout') }}" method="post">
                @csrf
                <button class="btn-2" type="submit">Esci</button>
            </form>
        </div>
    </section>

    @if ($clients->isNotEmpty())
        <section class="section-small bottom-line">
            <div class="wrapper">
                <div class="stack-mid text-container">
                    <p class="pill">GALLERIE PRIVATE</p>
                    <h2>I tuoi servizi fotografici.</h2>
                </div>

                <div class="grid-2 top-margin-xl small-gap">
                    @foreach ($clients as $client)
                        <a class="portfolio-card" href="{{ route('client-area.clients.show', $client) }}"
                            style="background-image: url('{{ $client->coverImageUrl() }}');">
                            <div class="portfolio-content display-none stack-small">
                                <h3>{{ $client->name }}</h3>
                                <h6>{{ $client->description }}</h6>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($workDeliveries->isNotEmpty())
        <section class="section-small">
            <div class="wrapper">
                <div class="stack-mid text-container">
                    <p class="pill">LINK DI CONSEGNA</p>
                    <h2>Materiale ad alta risoluzione.</h2>
                </div>

                <div class="client-delivery-list top-margin-xl">
                    @foreach ($workDeliveries as $workDelivery)
                        <article class="client-delivery-row">
                            <div class="stack-small">
                                <p class="color-disabled">{{ $workDelivery->work_date->format('d/m/Y') }}</p>
                                <h3>{{ $workDelivery->client_name }}</h3>
                                <p>{{ $workDelivery->work_description }}</p>
                                @if ($workDelivery->identifier_code)
                                    <p class="color-disabled">Codice {{ $workDelivery->identifier_code }}</p>
                                @endif
                            </div>

                            <a class="btn-2" href="{{ route('client-area.show', $workDelivery) }}">Apri consegna</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
