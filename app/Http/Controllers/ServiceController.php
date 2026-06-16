<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $services = $this->services();
        $categories = $services->pluck('category')->unique()->values();
        $selectedCategory = $request->query('categoria');
        $activeCategory = $categories->first(fn ($category) => Str::slug($category) === $selectedCategory);

        $visibleServices = $activeCategory
            ? $services->where('category', $activeCategory)->values()
            : $services;

        return view('servizi', [
            'services' => $visibleServices,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
        ]);
    }

    private function services(): Collection
    {
        return collect([
            [
                'category' => 'Ritratti',
                'title' => 'Ritratti per persone, professionisti e personalità del territorio.',
                'description' => "Sessioni pensate per restituire un'immagine autentica, curata e riconoscibile, adatta a comunicazione personale, editoriale o professionale.",
                'steps' => [
                    'Brief iniziale e definizione del tono visivo',
                    'Shooting guidato, naturale e senza pose forzate',
                    'Selezione e post-produzione delle immagini finali',
                ],
                'image' => 'https://assets-global.website-files.com/65f45868d16d48662164da00/65fa099d2c6b098ca488f97d_Image%20037.webp',
            ],
            [
                'category' => 'Eventi e location',
                'title' => 'Raccontare luoghi, format ed eventi con immagini chiare e vive.',
                'description' => 'Dalle atmosfere di Lucca Comics and Games agli eventi privati e culturali, il reportage segue il ritmo della giornata senza perdere i dettagli importanti.',
                'steps' => [
                    'Sopralluogo o confronto preliminare sul programma',
                    'Copertura fotografica discreta e continuativa',
                    'Gallery pronta per archivio, stampa e comunicazione',
                ],
                'image' => 'https://assets-global.website-files.com/65f45868d16d48662164da00/65fa099d2c6b098ca488f97d_Image%20037.webp',
            ],
            [
                'category' => 'Brand e prodotti',
                'title' => 'Immagini per brand che vogliono comunicare identità, qualità e carattere.',
                'description' => 'Dagli shooting prodotto ai contenuti social, Michele lavora su set essenziali e coerenti, anche per marchi con visibilità internazionale come sunsetersbrand.com.',
                'steps' => [
                    'Definizione mood, uso finale e riferimenti visivi',
                    'Shooting prodotto, lifestyle o campagna',
                    'File ottimizzati per web, social e materiali promozionali',
                ],
                'image' => 'https://assets-global.website-files.com/65f45868d16d48662164da00/65fa099d2c6b098ca488f97d_Image%20037.webp',
            ],
        ]);
    }
}
