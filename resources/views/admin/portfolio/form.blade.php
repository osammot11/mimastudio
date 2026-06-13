<form class="admin-form" action="{{ $action }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <section class="admin-card admin-form-section">
        <h2>Contenuti</h2>

        <div class="admin-field">
            <label for="title">Titolo</label>
            <input id="title" type="text" name="title" value="{{ old('title', $project->title) }}" required>
        </div>

        <div class="admin-field">
            <label for="slug">Slug</label>
            <input id="slug" type="text" name="slug" value="{{ old('slug', $project->slug) }}" placeholder="Lascia vuoto per generarlo automaticamente">
            <p class="admin-help">Serve per l'URL pubblico della scheda.</p>
        </div>

        <div class="admin-field">
            <label for="description">Descrizione breve</label>
            <input id="description" type="text" name="description" value="{{ old('description', $project->description) }}" required>
        </div>

        <div class="admin-field">
            <label for="body">Testo lungo</label>
            <textarea id="body" name="body" rows="8">{{ old('body', $project->body) }}</textarea>
        </div>
    </section>

    <section class="admin-card admin-form-section">
        <h2>Pubblicazione</h2>

        <div class="admin-grid-2">
            <div class="admin-field">
                <label for="category">Categoria</label>
                <input id="category" type="text" name="category" value="{{ old('category', $project->category) }}">
            </div>

            <div class="admin-field">
                <label for="sort_order">Ordine</label>
                <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $project->sort_order ?? 0) }}" min="0" required>
            </div>
        </div>

        <label class="admin-check">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $project->is_published ?? true))>
            <span>Pubblicato</span>
        </label>
    </section>

    <section class="admin-card admin-form-section">
        <h2>Cover</h2>

        <div class="admin-media-field">
            @if ($project->exists)
                <img class="admin-preview" src="{{ $project->coverImageUrl() }}" alt="{{ $project->title }}">
            @endif
            <div class="admin-field">
                <label for="cover_image">Immagine di copertina</label>
                <input id="cover_image" type="file" name="cover_image" accept="image/*" @required(! $project->exists)>
                <p class="admin-help">{{ $project->exists ? 'Lascia vuoto per mantenere quella attuale.' : 'Obbligatoria per mostrare la card pubblica.' }}</p>
            </div>
        </div>
    </section>

    <section class="admin-card admin-form-section">
        <h2>Gallery</h2>

        @if ($project->exists && $project->images->isNotEmpty())
            <div class="admin-gallery">
            @foreach ($project->images as $image)
                <div class="admin-gallery-row">
                    <img src="{{ $image->imageUrl() }}" alt="{{ $image->alt_text ?: $project->title }}">
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
        <a class="admin-btn" href="{{ route('admin.portfolio.index') }}">Annulla</a>
    </div>
</form>
