<?php

namespace App\Console\Commands;

use App\Models\GalleryUploadSession;
use App\Services\GalleryImageProcessor;
use Illuminate\Console\Command;

class PruneGalleryUploads extends Command
{
    protected $signature = 'gallery-uploads:prune';

    protected $description = 'Elimina sessioni gallery scadute e relativi file temporanei';

    public function handle(GalleryImageProcessor $processor): int
    {
        $deleted = 0;

        GalleryUploadSession::query()
            ->where('expires_at', '<=', now())
            ->with('items')
            ->chunkById(50, function ($sessions) use ($processor, &$deleted) {
                foreach ($sessions as $session) {
                    if ($session->status !== 'completed') {
                        foreach ($session->items as $item) {
                            $processor->deleteStaged($item->staged_path, $item->staged_thumbnail_path);
                        }
                    }

                    $session->delete();
                    $deleted++;
                }
            });

        $this->info("Sessioni eliminate: {$deleted}");

        return self::SUCCESS;
    }
}
