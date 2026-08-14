<form class="admin-form" action="{{ $action }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <section class="admin-card admin-form-section">
        <h2>Dati cliente</h2>

        <div class="admin-field">
            <label for="name">Nome completo o ragione sociale</label>
            <input id="name" type="text" name="name" value="{{ old('name', $customer->name) }}" required>
        </div>

        <div class="admin-field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $customer->email) }}"
                autocomplete="email" required>
            <p class="admin-help">Questa email identifica univocamente l'area riservata del cliente.</p>
        </div>

        <div class="admin-media-field">
            @if ($customer->logo_path)
                <img class="admin-preview" src="{{ $customer->logoUrl() }}" alt="Logo {{ $customer->name }}">
            @endif

            <div class="admin-field">
                <label for="logo">Logo cliente</label>
                <input id="logo" type="file" name="logo" accept="image/png,.png">
                <p class="admin-help">PNG opzionale, massimo 4 MB. Verrà mostrato nello slider della homepage.</p>

                @if ($customer->logo_path)
                    <label class="admin-check">
                        <input type="checkbox" name="remove_logo" value="1">
                        Rimuovi il logo attuale
                    </label>
                @endif
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="admin-errors">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="admin-actions">
        <button class="admin-btn primary" type="submit">Salva</button>
        <a class="admin-btn" href="{{ route('admin.customers.index') }}">Annulla</a>
    </div>
</form>
