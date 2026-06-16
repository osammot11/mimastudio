<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientImage;
use App\Models\PortfolioProject;
use App\Models\PortfolioProjectImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            User::updateOrCreate(
                ['email' => env('ADMIN_EMAIL')],
                [
                    'name' => env('ADMIN_NAME', 'Admin'),
                    'password' => Hash::make(env('ADMIN_PASSWORD')),
                ]
            );
        }

        $projects = [
            ['portrait-stories', 'Ritratti lucchesi', 'Ritratti', 'Volti, professionisti e personalità del territorio raccontati con luce naturale e direzione discreta.', 'Una selezione di ritratti dedicati a persone e figure riconoscibili della scena lucchese. Il lavoro punta su naturalezza, presenza e pulizia dell’immagine.'],
            ['studio-corners', 'Luoghi ed eventi', 'Eventi', 'Reportage fotografici per contesti culturali, location e format pubblici legati anche alla scena lucchese.', 'Dagli spazi cittadini agli eventi più dinamici, il servizio segue il ritmo del luogo e restituisce immagini utili per comunicazione, archivio e racconto.'],
            ['product-notes', 'Brand e prodotto', 'Brand', 'Immagini pulite e contemporanee per prodotti, campagne e contenuti digitali di marchi italiani e internazionali.', 'Shooting pensati per valorizzare materiali, dettagli e identità del brand, con file pronti per web, social e comunicazione commerciale.'],
            ['wedding-light', 'Cerimonie', 'Wedding', 'Racconti fotografici spontanei per matrimoni, eventi privati e momenti familiari importanti.', 'Un approccio discreto alla cerimonia, attento ai gesti reali e all’atmosfera della giornata, senza costruzioni inutili.'],
            ['editorial-mood', 'Editoriale e campagne', 'Editoriale', 'Servizi visuali per raccontare persone, luoghi e prodotti con un taglio editoriale riconoscibile.', 'Immagini costruite intorno a mood, contesto e destinazione finale, dalla pubblicazione social alla comunicazione più strutturata.'],
            ['everyday-icons', 'Branding personale', 'Branding', 'Ritratti e contenuti per professionisti che vogliono presentarsi con immagini solide, vere e contemporanee.', 'Una serie pensata per chi lavora con la propria immagine: imprenditori, artisti, creativi e figure pubbliche del territorio.'],
        ];

        foreach ($projects as $index => [$slug, $title, $category, $description, $body]) {
            $project = PortfolioProject::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'description' => $description,
                    'body' => $body,
                    'cover_image' => 'images/portfolio-'.($index + 1).'.jpeg',
                    'category' => $category,
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ]
            );

            foreach ([1, 2] as $galleryIndex) {
                PortfolioProjectImage::updateOrCreate(
                    [
                        'portfolio_project_id' => $project->id,
                        'sort_order' => $galleryIndex,
                    ],
                    [
                        'image_path' => 'images/portfolio-'.((($index + $galleryIndex + 1) % 6) + 1).'.jpeg',
                        'alt_text' => $title,
                    ]
                );
            }
        }

        $clients = [
            ['Arianna Studio', 'Ritratti', 'Ritratti e contenuti visuali per una realtà creativa del territorio lucchese.', '2026-01-12'],
            ['Casa Velata', 'Location', 'Racconto fotografico di uno spazio ricettivo, tra dettagli, luce e atmosfera.', '2026-02-18'],
            ['Linea Forma', 'Brand', 'Immagini prodotto per catalogo, social e comunicazione digitale.', '2026-03-22'],
            ['Marea Events', 'Eventi', 'Reportage essenziale per un evento privato seguito con discrezione.', '2026-04-09'],
        ];

        foreach ($clients as $index => [$name, $category, $description, $date]) {
            $client = Client::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'category' => $category,
                    'description' => $description,
                    'client_date' => $date,
                    'photo_image' => 'images/portfolio-'.($index + 1).'.jpeg',
                    'cover_image' => 'images/portfolio-'.($index + 2).'.jpeg',
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ]
            );

            foreach ([1, 2] as $galleryIndex) {
                ClientImage::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'sort_order' => $galleryIndex,
                    ],
                    [
                        'image_path' => 'images/portfolio-'.((($index + $galleryIndex + 1) % 6) + 1).'.jpeg',
                        'alt_text' => $name,
                    ]
                );
            }
        }
    }
}
