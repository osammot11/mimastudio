<?php

namespace App\Console\Commands;

use App\Models\ClientImage;
use App\Services\GalleryImageProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateGalleryThumbnails extends Command
{
    protected $signature = 'gallery:generate-thumbnails {--force : Rigenera anche le miniature esistenti}';

    protected $description = 'Genera miniature WebP e metadati per le immagini gallery esistenti';

    public function handle(GalleryImageProcessor $processor): int
    {
        $query = ClientImage::query();

        if (! $this->option('force')) {
            $query->whereNull('thumbnail_path');
        }

        $processed = 0;
        $skipped = 0;

        $query->chunkById(50, function ($images) use ($processor, &$processed, &$skipped) {
            foreach ($images as $image) {
                $previousThumbnail = $image->thumbnail_path;
                $result = $processor->generateThumbnailForExisting($image->image_path);

                if (! $result) {
                    $skipped++;

                    continue;
                }

                $size = @filesize(storage_path('app/public/'.$image->image_path));
                $image->update([
                    'thumbnail_path' => $result['thumbnailPath'],
                    'width' => $result['width'],
                    'height' => $result['height'],
                    'byte_size' => $image->byte_size ?: ($size === false ? null : $size),
                ]);

                if ($previousThumbnail && $previousThumbnail !== $result['thumbnailPath']) {
                    Storage::disk('public')->delete($previousThumbnail);
                }
                $processed++;
            }
        });

        $this->info("Miniature generate: {$processed}; immagini saltate: {$skipped}");

        return self::SUCCESS;
    }
}
