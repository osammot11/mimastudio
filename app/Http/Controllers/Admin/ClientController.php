<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClientWorkReady;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('admin.clients.create', [
            'client' => new Client([
                'is_published' => true,
                'is_portal_visible' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedClientData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);
        $data['email'] = strtolower(trim($data['email']));
        $data['is_published'] = $request->boolean('is_published');
        $data['is_portal_visible'] = $request->boolean('is_portal_visible');
        unset($data['send_notification']);
        $data['photo_image'] = $request->file('photo_image')->store('clients', 'public');
        $data['cover_image'] = $request->file('cover_image')->store('clients', 'public');

        $client = Client::create($data);
        $this->storeGalleryImages($request, $client);

        return $this->savedResponse($request, $client, 'Cliente creato.');
    }

    public function edit(Client $client): View
    {
        $client->load('images');

        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $data = $this->validatedClientData($request, $client);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name'], $client);
        $data['email'] = strtolower(trim($data['email']));
        $data['is_published'] = $request->boolean('is_published');
        $data['is_portal_visible'] = $request->boolean('is_portal_visible');
        unset($data['send_notification']);

        if ($request->hasFile('photo_image')) {
            $this->deleteStoredFile($client->photo_image);
            $data['photo_image'] = $request->file('photo_image')->store('clients', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $this->deleteStoredFile($client->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('clients', 'public');
        }

        $client->update($data);
        $this->updateGalleryImages($request, $client);
        $this->deleteGalleryImages($request, $client);
        $this->storeGalleryImages($request, $client);

        return $this->savedResponse($request, $client, 'Cliente aggiornato.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->load('images');

        $this->deleteStoredFile($client->photo_image);
        $this->deleteStoredFile($client->cover_image);

        foreach ($client->images as $image) {
            $this->deleteStoredFile($image->image_path);
        }

        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Cliente eliminato.');
    }

    private function validatedClientData(Request $request, ?Client $client = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('clients', 'slug')->ignore($client),
            ],
            'description' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'client_date' => ['nullable', 'date'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'send_notification' => ['nullable', 'boolean'],
            'is_portal_visible' => ['nullable', 'boolean'],
            'photo_image' => [$client ? 'nullable' : 'required', 'image', 'max:4096'],
            'cover_image' => [$client ? 'nullable' : 'required', 'image', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:4096'],
            'image_alt' => ['nullable', 'array'],
            'image_alt.*' => ['nullable', 'string', 'max:255'],
            'image_sort_order' => ['nullable', 'array'],
            'image_sort_order.*' => ['nullable', 'integer', 'min:0'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:client_images,id'],
        ]);
    }

    private function uniqueSlug(string $source, ?Client $client = null): string
    {
        $baseSlug = Str::slug($source);
        $slug = $baseSlug;
        $counter = 2;

        while (Client::where('slug', $slug)
            ->when($client, fn ($query) => $query->whereKeyNot($client->getKey()))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function storeGalleryImages(Request $request, Client $client): void
    {
        foreach ($request->file('gallery_images', []) as $image) {
            $client->images()->create([
                'image_path' => $image->store('clients', 'public'),
                'alt_text' => $client->name,
                'sort_order' => ($client->images()->max('sort_order') ?? 0) + 1,
            ]);
        }
    }

    private function updateGalleryImages(Request $request, Client $client): void
    {
        foreach ($request->input('image_alt', []) as $imageId => $altText) {
            $image = $client->images()->whereKey($imageId)->first();

            if (! $image) {
                continue;
            }

            $image->update([
                'alt_text' => $altText,
                'sort_order' => (int) $request->input("image_sort_order.{$imageId}", $image->sort_order),
            ]);
        }
    }

    private function deleteGalleryImages(Request $request, Client $client): void
    {
        $images = $client->images()->whereIn('id', $request->input('delete_images', []))->get();

        foreach ($images as $image) {
            $this->deleteStoredFile($image->image_path);
            $image->delete();
        }
    }

    private function deleteStoredFile(?string $path): void
    {
        if (! $path || str_starts_with($path, 'images/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function savedResponse(
        Request $request,
        Client $client,
        string $successMessage,
    ): RedirectResponse {
        $redirect = redirect()->route('admin.clients.edit', $client);

        if (! $request->boolean('send_notification')) {
            return $redirect->with('status', $successMessage);
        }

        try {
            Mail::to($client->email)->send(
                new ClientWorkReady($client, $this->clientAccessUrl($client)),
            );

            return $redirect->with(
                'status',
                $successMessage.' Email di notifica inviata.',
            );
        } catch (Throwable $exception) {
            report($exception);

            return $redirect
                ->with('status', $successMessage)
                ->with(
                    'status_error',
                    'Il cliente è stato salvato, ma l’email di notifica non è stata inviata.',
                );
        }
    }

    private function clientAccessUrl(Client $client): ?string
    {
        if ($client->is_portal_visible) {
            return URL::temporarySignedRoute(
                'client-area.authenticate',
                now()->addDays(7),
                [
                    'token' => Crypt::encryptString($client->email),
                    'client' => $client->getKey(),
                ],
            );
        }

        return $client->is_published
            ? route('clienti.show', $client)
            : null;
    }
}
