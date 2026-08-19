<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Customer;
use App\Services\ClientWorkNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ClientController extends Controller
{
    public function __construct(private ClientWorkNotifier $notifier) {}

    public function index(): View
    {
        $clients = Client::query()
            ->with('customer')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.clients.index', compact('clients'));
    }

    public function create(): View
    {
        $customers = $this->customers();
        $selectedCustomerId = request()->integer('customer');

        return view('admin.clients.create', [
            'client' => new Client([
                'is_published' => true,
                'is_portal_visible' => true,
                'sort_order' => 0,
            ]),
            'customers' => $customers,
            'defaultCustomerMode' => $customers->isEmpty() ? 'new' : 'existing',
            'selectedCustomerId' => $customers->contains('id', $selectedCustomerId)
                ? $selectedCustomerId
                : null,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validatedClientData($request);
        $customer = $this->resolveCustomer($data);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name']);
        $data['customer_id'] = $customer->getKey();
        $data['is_published'] = $request->boolean('is_published');
        $data['is_portal_visible'] = $request->boolean('is_portal_visible');
        $data['video_url'] = $request->boolean('has_video')
            ? ($data['video_url'] ?? null)
            : null;
        $this->removeCustomerFields($data);
        $data['photo_image'] = $request->file('photo_image')->store('clients', 'public');
        $data['cover_image'] = $request->file('cover_image')->store('clients', 'public');

        $client = Client::create($data);
        $this->storeGalleryImages($request, $client);

        return $this->savedResponse($request, $client, 'Lavoro creato.');
    }

    public function edit(Client $client): View
    {
        $client->load('customer');

        return view('admin.clients.edit', [
            'client' => $client,
            'galleryImages' => $client->images()->paginate(
                (int) config('gallery.admin_page_size'),
                ['*'],
                'gallery_page',
            ),
            'activeUploadSession' => $client->galleryUploadSessions()
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->latest('id')
                ->first(),
            'customers' => $this->customers(),
            'defaultCustomerMode' => 'existing',
            'selectedCustomerId' => $client->customer_id,
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse|JsonResponse
    {
        $data = $this->validatedClientData($request, $client);
        $customer = $this->resolveCustomer($data);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['name'], $client);
        $data['customer_id'] = $customer->getKey();
        $data['is_published'] = $request->boolean('is_published');
        $data['is_portal_visible'] = $request->boolean('is_portal_visible');
        $data['video_url'] = $request->boolean('has_video')
            ? ($data['video_url'] ?? null)
            : null;
        $this->removeCustomerFields($data);

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

        return $this->savedResponse($request, $client, 'Lavoro aggiornato.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->load('images');

        $this->deleteStoredFile($client->photo_image);
        $this->deleteStoredFile($client->cover_image);

        foreach ($client->images as $image) {
            $this->deleteStoredFile($image->image_path);
            $this->deleteStoredFile($image->thumbnail_path);
        }

        foreach ($client->galleryUploadSessions()->with('items')->get() as $uploadSession) {
            foreach ($uploadSession->items as $item) {
                Storage::disk('local')->delete(array_filter([
                    $item->staged_path,
                    $item->staged_thumbnail_path,
                ]));
            }
        }

        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Lavoro eliminato.');
    }

    private function validatedClientData(Request $request, ?Client $client = null): array
    {
        $request->merge([
            'new_customer_email' => strtolower(trim((string) $request->input('new_customer_email'))),
        ]);

        return $request->validate([
            'customer_mode' => ['required', Rule::in(['existing', 'new'])],
            'customer_id' => [
                'nullable',
                'required_if:customer_mode,existing',
                'integer',
                'exists:customers,id',
            ],
            'new_customer_name' => [
                'nullable',
                'required_if:customer_mode,new',
                'string',
                'max:255',
            ],
            'new_customer_email' => [
                'nullable',
                'required_if:customer_mode,new',
                'email',
                'max:255',
                Rule::unique('customers', 'email'),
            ],
            'name' => ['required', 'string', 'max:255'],
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
            'defer_notification' => ['nullable', 'boolean'],
            'is_portal_visible' => ['nullable', 'boolean'],
            'has_video' => ['nullable', 'boolean'],
            'video_url' => [
                'nullable',
                'required_if:has_video,1',
                'url:http,https',
                'max:2048',
            ],
            'high_resolution_url' => [
                'nullable',
                'url:http,https',
                'max:2048',
            ],
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
        $nextOrder = ((int) $client->images()->max('sort_order')) + 1;

        foreach ($request->file('gallery_images', []) as $image) {
            $client->images()->create([
                'image_path' => $image->store('clients', 'public'),
                'original_name' => $image->getClientOriginalName(),
                'byte_size' => $image->getSize(),
                'alt_text' => $client->name,
                'sort_order' => $nextOrder++,
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
            $this->deleteStoredFile($image->thumbnail_path);
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
    ): RedirectResponse|JsonResponse {
        $redirect = redirect()->route('admin.clients.edit', $client);

        if ($request->expectsJson()) {
            return response()->json([
                'client_id' => $client->getKey(),
                'client_slug' => $client->slug,
                'redirect_url' => route('admin.clients.edit', $client),
                'upload_url' => route('admin.clients.gallery-uploads.store', $client),
                'notification_requested' => $request->boolean('send_notification'),
            ]);
        }

        if (! $request->boolean('send_notification')) {
            return $redirect->with('status', $successMessage);
        }

        try {
            $this->notifier->send($client);

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

    private function resolveCustomer(array $data): Customer
    {
        if ($data['customer_mode'] === 'existing') {
            return Customer::findOrFail($data['customer_id']);
        }

        return Customer::create([
            'name' => $data['new_customer_name'],
            'email' => $data['new_customer_email'],
        ]);
    }

    private function removeCustomerFields(array &$data): void
    {
        unset(
            $data['customer_mode'],
            $data['new_customer_name'],
            $data['new_customer_email'],
            $data['send_notification'],
            $data['defer_notification'],
            $data['has_video'],
        );
    }

    private function customers()
    {
        return Customer::query()
            ->orderBy('name')
            ->get();
    }
}
