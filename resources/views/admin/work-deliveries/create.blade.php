@extends('layouts.admin')

@section('title', 'Nuova consegna - Admin Mima Studio')
@section('page-title', 'Nuova consegna')
@section('eyebrow', 'Consegna lavoro')

@section('actions')
    <a class="admin-btn" href="{{ route('admin.work-deliveries.index') }}">Annulla</a>
@endsection

@section('content')
    <form class="admin-form" action="{{ route('admin.work-deliveries.store') }}" method="post">
        @csrf

        @if ($errors->any())
            <div class="admin-errors">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="admin-card admin-form-section">
            <h2>Cliente e lavoro</h2>

            <div class="admin-grid-2">
                <div class="admin-field">
                    <label for="client_name">Nome cliente</label>
                    <input id="client_name" type="text" name="client_name"
                        value="{{ old('client_name', $workDelivery->client_name) }}" required>
                </div>

                <div class="admin-field">
                    <label for="email">Email cliente</label>
                    <input id="email" type="email" name="email"
                        value="{{ old('email', $workDelivery->email) }}" required>
                </div>
            </div>

            <div class="admin-field">
                <label for="work_description">Descrizione lavoro</label>
                <textarea id="work_description" name="work_description" rows="6" required>{{ old('work_description', $workDelivery->work_description) }}</textarea>
            </div>

            <div class="admin-grid-2">
                <div class="admin-field">
                    <label for="work_date">Data</label>
                    <input id="work_date" type="date" name="work_date"
                        value="{{ old('work_date', optional($workDelivery->work_date)->format('Y-m-d')) }}" required>
                </div>

                <div class="admin-field">
                    <label for="identifier_code">Codice identificativo</label>
                    <input id="identifier_code" type="text" name="identifier_code"
                        value="{{ old('identifier_code', $workDelivery->identifier_code) }}"
                        placeholder="Facoltativo">
                </div>
            </div>
        </section>

        <section class="admin-card admin-form-section">
            <h2>Galleria esterna</h2>

            <div class="admin-field">
                <label for="gallery_url">Link al lavoro</label>
                <input id="gallery_url" type="url" name="gallery_url"
                    value="{{ old('gallery_url', $workDelivery->gallery_url) }}"
                    placeholder="https://..." required>
                <p class="admin-help">Può essere un link WeTransfer, Amazon Photos o un'altra galleria HTTPS accessibile al cliente.</p>
            </div>
        </section>

        <div>
            <button class="admin-btn primary" type="submit">Salva e invia email</button>
        </div>
    </form>
@endsection
