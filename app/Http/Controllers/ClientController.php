<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('clienti', compact('clients'));
    }

    public function show(Client $client): View
    {
        abort_unless($client->is_published, 404);

        $client->load('images');

        return view('clienti-show', compact('client'));
    }
}
