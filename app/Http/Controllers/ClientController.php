<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Client::query()
            ->published()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category')
            ->unique()
            ->values();

        $selectedCategory = $request->query('categoria');
        $activeCategory = $categories->first(fn ($category) => Str::slug($category) === $selectedCategory);

        $clients = Client::query()
            ->published()
            ->when($activeCategory, fn ($query) => $query->where('category', $activeCategory))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('clienti', compact('clients', 'categories', 'activeCategory'));
    }

    public function show(Client $client): View
    {
        abort_unless($client->is_published, 404);

        $galleryImages = $client->images()->cursorPaginate((int) config('gallery.page_size'));

        return view('clienti-show', compact('client', 'galleryImages'));
    }

    public function gallery(Client $client): JsonResponse
    {
        abort_unless($client->is_published, 404);

        return $this->galleryResponse($client);
    }

    private function galleryResponse(Client $client): JsonResponse
    {
        $images = $client->images()->cursorPaginate((int) config('gallery.page_size'));

        return response()->json([
            'items' => collect($images->items())->map(fn ($image) => [
                'id' => $image->getKey(),
                'thumbnail_url' => $image->thumbnailUrl(),
                'image_url' => $image->imageUrl(),
                'alt' => $image->alt_text ?: $client->name,
                'width' => $image->width,
                'height' => $image->height,
            ])->values(),
            'next_cursor' => $images->nextCursor()?->encode(),
        ]);
    }
}
