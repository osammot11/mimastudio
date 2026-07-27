<form class="admin-form" action="{{ $action }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @php
        $customerMode = old('customer_mode', $defaultCustomerMode);
        $currentCustomerId = old('customer_id', $selectedCustomerId);
    @endphp

    <section class="admin-card admin-form-section">
        <h2>Cliente</h2>

        <div class="admin-field">
            <label for="customer_mode">Come vuoi associare il cliente?</label>
            <select id="customer_mode" name="customer_mode" data-customer-mode required>
                <option value="existing" @selected($customerMode === 'existing') @disabled($customers->isEmpty())>
                    Cliente esistente
                </option>
                <option value="new" @selected($customerMode === 'new')>Nuovo cliente</option>
            </select>
        </div>

        <div class="admin-field" data-customer-panel="existing" @hidden($customerMode !== 'existing')>
            <label for="customer_id">Cliente esistente</label>
            <select id="customer_id" name="customer_id" @required($customerMode === 'existing')>
                <option value="">Seleziona un cliente</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}" @selected((string) $currentCustomerId === (string) $customer->id)>
                        {{ $customer->name }} · {{ $customer->email ?: 'email da inserire' }}
                    </option>
                @endforeach
            </select>
            <p class="admin-help">Nome ed email vengono recuperati dall'anagrafica.</p>
        </div>

        <div class="admin-grid-2" data-customer-panel="new" @hidden($customerMode !== 'new')>
            <div class="admin-field">
                <label for="new_customer_name">Nome nuovo cliente</label>
                <input id="new_customer_name" type="text" name="new_customer_name"
                    value="{{ old('new_customer_name') }}" @required($customerMode === 'new')>
            </div>
            <div class="admin-field">
                <label for="new_customer_email">Email nuovo cliente</label>
                <input id="new_customer_email" type="email" name="new_customer_email"
                    value="{{ old('new_customer_email') }}" autocomplete="email" @required($customerMode === 'new')>
            </div>
        </div>
    </section>

    <section class="admin-card admin-form-section">
        <h2>Contenuti del lavoro</h2>

        <div class="admin-field">
            <label for="name">Titolo lavoro</label>
            <input id="name" type="text" name="name" value="{{ old('name', $client->name) }}" required>
        </div>

        <div class="admin-field">
            <label for="slug">Slug</label>
            <input id="slug" type="text" name="slug" value="{{ old('slug', $client->slug) }}" placeholder="Lascia vuoto per generarlo automaticamente">
            <p class="admin-help">Serve per l'URL pubblico della scheda.</p>
        </div>

        <div class="admin-field">
            <label for="description">Descrizione breve</label>
            <input id="description" type="text" name="description" value="{{ old('description', $client->description) }}" required>
        </div>

        <div class="admin-field">
            <label for="category">Categoria</label>
            <input id="category" type="text" name="category" value="{{ old('category', $client->category) }}" placeholder="Es. Brand, Eventi, Ritratti">
        </div>
    </section>

    <section class="admin-card admin-form-section">
        <h2>Pubblicazione</h2>

        <div class="admin-grid-2">
            <div class="admin-field">
                <label for="client_date">Data</label>
                <input id="client_date" type="date" name="client_date" value="{{ old('client_date', optional($client->client_date)->format('Y-m-d')) }}">
            </div>

            <div class="admin-field">
                <label for="sort_order">Ordine</label>
                <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $client->sort_order ?? 0) }}" min="0" required>
            </div>
        </div>

        <label class="admin-toggle">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $client->is_published ?? true))>
            <span class="admin-toggle-control" aria-hidden="true"></span>
            <span class="admin-toggle-copy">
                <strong>Visibile sul sito pubblico</strong>
                <small>Mostra il lavoro nello storefront pubblico, nella sezione Clienti.</small>
            </span>
        </label>

        <label class="admin-toggle">
            <input type="checkbox" name="send_notification" value="1" @checked(old('send_notification', false))>
            <span class="admin-toggle-control" aria-hidden="true"></span>
            <span class="admin-toggle-copy">
                <strong>Invia email di notifica</strong>
                <small>Invia al cliente una notifica del nuovo lavoro dopo il salvataggio. Si disattiva automaticamente.</small>
            </span>
        </label>

        <label class="admin-toggle">
            <input type="checkbox" name="is_portal_visible" value="1" @checked(old('is_portal_visible', $client->is_portal_visible ?? true))>
            <span class="admin-toggle-control" aria-hidden="true"></span>
            <span class="admin-toggle-copy">
                <strong>Visibile nell'area riservata</strong>
                <small>Mostra il lavoro nel portale associato all'email del cliente.</small>
            </span>
        </label>
    </section>

    <section class="admin-card admin-form-section">
        <h2>Media principali</h2>

        <div class="admin-media-field">
            @if ($client->exists)
                <img class="admin-preview" src="{{ $client->photoImageUrl() }}" alt="{{ $client->name }}">
            @endif
            <div class="admin-field">
                <label for="photo_image">Foto cliente</label>
                <input id="photo_image" type="file" name="photo_image" accept="image/*" @required(! $client->exists)>
                <p class="admin-help">{{ $client->exists ? 'Lascia vuoto per mantenere quella attuale.' : 'Obbligatoria.' }}</p>
            </div>
        </div>

        <div class="admin-media-field">
            @if ($client->exists)
                <img class="admin-preview" src="{{ $client->coverImageUrl() }}" alt="{{ $client->name }}">
            @endif
            <div class="admin-field">
                <label for="cover_image">Immagine di copertina</label>
                <input id="cover_image" type="file" name="cover_image" accept="image/*" @required(! $client->exists)>
                <p class="admin-help">{{ $client->exists ? 'Lascia vuoto per mantenere quella attuale.' : 'Obbligatoria per mostrare la card pubblica.' }}</p>
            </div>
        </div>
    </section>

    <section class="admin-card admin-form-section">
        <h2>Gallery</h2>

        @if ($client->exists && $client->images->isNotEmpty())
            <div class="admin-gallery">
            @foreach ($client->images as $image)
                <div class="admin-gallery-row">
                    <img src="{{ $image->imageUrl() }}" alt="{{ $image->alt_text ?: $client->name }}">
                    <div class="admin-field">
                        <label for="image_alt_{{ $image->id }}">Alt text</label>
                        <input id="image_alt_{{ $image->id }}" type="text" name="image_alt[{{ $image->id }}]" value="{{ old("image_alt.{$image->id}", $image->alt_text) }}">
                    </div>
                    <div class="admin-field">
                        <label for="image_sort_order_{{ $image->id }}">Ordine</label>
                        <input id="image_sort_order_{{ $image->id }}" type="number" name="image_sort_order[{{ $image->id }}]" value="{{ old("image_sort_order.{$image->id}", $image->sort_order) }}" min="0">
                    </div>
                    <label class="admin-check">
                        <input type="checkbox" name="delete_images[]" value="{{ $image->id }}">
                        <span>Elimina</span>
                    </label>
                </div>
            @endforeach
            </div>
        @else
            <p class="admin-help">Nessuna immagine gallery inserita.</p>
        @endif

        <div class="admin-field">
            <label for="gallery_images">Aggiungi immagini</label>
            <input id="gallery_images" type="file" name="gallery_images[]" accept="image/*" multiple>
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
        <a class="admin-btn" href="{{ route('admin.clients.index') }}">Annulla</a>
    </div>

    <script>
        (() => {
            const mode = document.querySelector('[data-customer-mode]');
            const panels = document.querySelectorAll('[data-customer-panel]');

            if (!mode || !panels.length) {
                return;
            }

            const updateCustomerFields = () => {
                panels.forEach(panel => {
                    const isActive = panel.dataset.customerPanel === mode.value;
                    panel.hidden = !isActive;

                    panel.querySelectorAll('input, select').forEach(field => {
                        field.required = isActive;
                        field.disabled = !isActive;
                    });
                });
            };

            mode.addEventListener('change', updateCustomerFields);
            updateCustomerFields();
        })();
    </script>
</form>
