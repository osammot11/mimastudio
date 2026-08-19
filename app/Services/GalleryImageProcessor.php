<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class GalleryImageProcessor
{
    private const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function stage(UploadedFile $file, string $sessionUuid): array
    {
        $source = $file->getRealPath();
        $imageInfo = $source ? @getimagesize($source) : false;
        $mime = is_array($imageInfo) ? ($imageInfo['mime'] ?? null) : null;

        if (! $source || ! isset(self::MIME_EXTENSIONS[$mime])) {
            throw new RuntimeException('Il file non contiene un’immagine JPEG, PNG o WebP valida.');
        }

        $pixelCount = ((int) $imageInfo[0]) * ((int) $imageInfo[1]);
        if ($pixelCount > (int) config('gallery.max_pixels')) {
            throw new RuntimeException('L’immagine supera il limite di 30 megapixel previsto per la gallery web.');
        }

        $basename = (string) Str::uuid();
        $imagePath = "gallery-uploads/{$sessionUuid}/{$basename}.".self::MIME_EXTENSIONS[$mime];
        $thumbnailPath = "gallery-uploads/{$sessionUuid}/{$basename}-thumb.webp";

        if (! Storage::disk('local')->putFileAs(dirname($imagePath), $file, basename($imagePath))) {
            throw new RuntimeException('Non è stato possibile salvare il file temporaneo.');
        }

        try {
            [$width, $height] = $this->createThumbnail($source, $mime, $thumbnailPath);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($imagePath);
            throw $exception;
        }

        return [
            'staged_path' => $imagePath,
            'staged_thumbnail_path' => $thumbnailPath,
            'width' => $width,
            'height' => $height,
        ];
    }

    public function promote(array $staged, int $clientId): array
    {
        $extension = pathinfo($staged['staged_path'], PATHINFO_EXTENSION);
        $basename = (string) Str::uuid();
        $imagePath = "clients/{$clientId}/gallery/{$basename}.{$extension}";
        $thumbnailPath = "clients/{$clientId}/gallery/{$basename}-thumb.webp";

        $this->moveBetweenLocalDisks($staged['staged_path'], $imagePath);

        try {
            $this->moveBetweenLocalDisks($staged['staged_thumbnail_path'], $thumbnailPath);
        } catch (\Throwable $exception) {
            $this->moveBackToStaging($imagePath, $staged['staged_path']);
            throw $exception;
        }

        return [
            'image_path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
        ];
    }

    public function rollbackPromotion(array $promoted, array $staged): void
    {
        $this->moveBackToStaging($promoted['image_path'], $staged['staged_path']);
        $this->moveBackToStaging($promoted['thumbnail_path'], $staged['staged_thumbnail_path']);
    }

    public function deleteStaged(?string ...$paths): void
    {
        Storage::disk('local')->delete(array_values(array_filter($paths)));
    }

    public function generateThumbnailForExisting(string $publicPath): ?array
    {
        if (! Storage::disk('public')->exists($publicPath)) {
            return null;
        }

        $source = Storage::disk('public')->path($publicPath);
        $imageInfo = @getimagesize($source);
        $mime = is_array($imageInfo) ? ($imageInfo['mime'] ?? null) : null;

        if (! isset(self::MIME_EXTENSIONS[$mime])) {
            return null;
        }

        if (((int) $imageInfo[0] * (int) $imageInfo[1]) > (int) config('gallery.max_pixels')) {
            return null;
        }

        $thumbnailPath = 'clients/thumbnails/'.Str::uuid().'.webp';
        [$width, $height] = $this->createThumbnail($source, $mime, $thumbnailPath, 'public');

        return compact('thumbnailPath', 'width', 'height');
    }

    private function createThumbnail(
        string $source,
        string $mime,
        string $thumbnailPath,
        string $disk = 'local',
    ): array {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            'image/webp' => @imagecreatefromwebp($source),
        };

        if (! $image) {
            throw new RuntimeException('Non è stato possibile elaborare l’immagine.');
        }

        try {
            if ($mime === 'image/jpeg') {
                $image = $this->applyExifOrientation($image, $source);
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $maxWidth = (int) config('gallery.thumbnail_width', 900);
            $targetWidth = min($width, $maxWidth);
            $targetHeight = max(1, (int) round($height * ($targetWidth / $width)));
            $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);
            $temporary = null;

            try {
                imagealphablending($thumbnail, false);
                imagesavealpha($thumbnail, true);
                imagefill($thumbnail, 0, 0, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
                imagealphablending($thumbnail, true);
                imagecopyresampled(
                    $thumbnail,
                    $image,
                    0,
                    0,
                    0,
                    0,
                    $targetWidth,
                    $targetHeight,
                    $width,
                    $height,
                );

                $temporary = tempnam(sys_get_temp_dir(), 'gallery-thumb-');

                if (! $temporary || ! imagewebp($thumbnail, $temporary, (int) config('gallery.thumbnail_quality', 78))) {
                    throw new RuntimeException('Non è stato possibile generare la miniatura.');
                }

                $stream = fopen($temporary, 'rb');
                $written = $stream && Storage::disk($disk)->writeStream($thumbnailPath, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                if (! $written) {
                    throw new RuntimeException('Non è stato possibile salvare la miniatura.');
                }
            } finally {
                imagedestroy($thumbnail);

                if ($temporary) {
                    @unlink($temporary);
                }
            }

            return [$width, $height];
        } finally {
            imagedestroy($image);
        }
    }

    private function applyExifOrientation(\GdImage $image, string $source): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = @exif_read_data($source)['Orientation'] ?? 1;
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => false,
        };

        if (! $rotated) {
            return $image;
        }

        imagedestroy($image);

        return $rotated;
    }

    private function moveBetweenLocalDisks(string $sourcePath, string $destinationPath): void
    {
        $source = Storage::disk('local')->path($sourcePath);
        $destination = Storage::disk('public')->path($destinationPath);

        File::ensureDirectoryExists(dirname($destination));

        if (! @rename($source, $destination)) {
            throw new RuntimeException('Non è stato possibile pubblicare un file della gallery.');
        }
    }

    private function moveBackToStaging(string $publicPath, string $stagedPath): void
    {
        $source = Storage::disk('public')->path($publicPath);

        if (! file_exists($source)) {
            return;
        }

        $destination = Storage::disk('local')->path($stagedPath);
        File::ensureDirectoryExists(dirname($destination));
        @rename($source, $destination);
    }
}
