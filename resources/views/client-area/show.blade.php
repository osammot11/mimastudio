@extends('layouts.app')

@section('title', 'Consegna del '.$workDelivery->work_date->format('d/m/Y').' - Michele Mariani')

@section('content')
    <section class="section-standard bottom-line">
        <div class="wrapper grid-2">
            <div class="stack-large text-container">
                <p class="pill">CONSEGNA PRIVATA</p>
                <h1>{{ $workDelivery->client_name }}</h1>
                <p>{{ $workDelivery->work_date->format('d/m/Y') }}</p>
            </div>

            <div class="center-vertical">
                <div class="stack-xl">
                    <p>{!! nl2br(e($workDelivery->work_description)) !!}</p>

                    @if ($workDelivery->identifier_code)
                        <p class="color-disabled">Codice identificativo: {{ $workDelivery->identifier_code }}</p>
                    @endif

                    <div class="client-portal-actions">
                        <a class="btn-2" href="{{ route('client-area.index') }}">Tutte le consegne</a>
                        <a class="btn-2 client-portal-primary" href="{{ $workDelivery->gallery_url }}"
                            target="_blank" rel="noopener noreferrer">
                            Visualizza e scarica il lavoro
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
