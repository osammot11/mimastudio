<?php

namespace App\Http\Controllers;

use App\Models\Client;
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

        $client->load('images');

        return view('clienti-show', compact('client'));
    }
}
