<form class="admin-form" action="{{ $action }}" method="post">
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
