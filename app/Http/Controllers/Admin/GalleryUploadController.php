<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientImage;
use App\Models\GalleryUploadItem;
use App\Models\GalleryUploadSession;
use App\Services\ClientWorkNotifier;
use App\Services\GalleryImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class GalleryUploadController extends Controller
{
    public function store(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'manifest' => ['required', 'array', 'min:1', 'max:'.config('gallery.max_files')],
            'manifest.*.fingerprint' => ['required', 'string', 'size:64'],
            'manifest.*.name' => ['required', 'string', 'max:255'],
            'manifest.*.size' => ['required', 'integer', 'min:1', 'max:'.config('gallery.max_file_kilobytes') * 1024],
            'manifest.*.position' => ['required', 'integer', 'min:0', 'max:999'],
            'notification_requested' => ['nullable', 'boolean'],
        ]);

        $manifest = collect($validated['manifest'])->sortBy('position')->values();
        $positions = $manifest->pluck('position');
        $fingerprints = $manifest->pluck('fingerprint');

        if ($positions->unique()->count() !== $manifest->count()
            || $fingerprints->unique()->count() !== $manifest->count()) {
            throw ValidationException::withMessages([
                'manifest' => 'Il manifest contiene file o posizioni duplicate.',
            ]);
        }

        $expectedBytes = (int) $manifest->sum('size');
        $manifestHash = hash('sha256', $fingerprints->implode('|'));
        $existing = $client->galleryUploadSessions()
            ->where('manifest_hash', $manifestHash)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if ($existing) {
            return response()->json($this->sessionPayload($existing));
        }

        $otherSession = $client->galleryUploadSessions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if ($otherSession) {
            return response()->json([
                'message' => 'Esiste già un caricamento incompleto per questo lavoro. Riprendilo o annullalo.',
                'session' => $this->sessionPayload($otherSession),
            ], 409);
        }

        $this->ensureDiskSpace($expectedBytes);

        $session = DB::transaction(function () use ($client, $request, $validated, $manifestHash, $expectedBytes, $manifest) {
            $session = $client->galleryUploadSessions()->create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $request->user()?->getKey(),
                'manifest_hash' => $manifestHash,
                'expected_files' => $manifest->count(),
                'expected_bytes' => $expectedBytes,
                'status' => 'active',
                'notification_requested' => (bool) ($validated['notification_requested'] ?? false),
                'expires_at' => now()->addDays((int) config('gallery.expires_days')),
            ]);

            $now = now();
            $manifest->chunk(250)->each(function ($items) use ($session, $now) {
                GalleryUploadItem::insert($items->map(fn (array $item) => [
                    'gallery_upload_session_id' => $session->getKey(),
                    'fingerprint' => $item['fingerprint'],
                    'original_name' => $item['name'],
                    'byte_size' => $item['size'],
                    'position' => $item['position'],
                    'status' => 'pending',
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all());
            });

            return $session;
        });

        return response()->json($this->sessionPayload($session), 201);
    }

    public function show(Client $client, GalleryUploadSession $galleryUploadSession): JsonResponse
    {
        $this->ensureSessionBelongsToClient($client, $galleryUploadSession);

        return response()->json($this->sessionPayload($galleryUploadSession));
    }

    public function batch(
        Request $request,
        Client $client,
        GalleryUploadSession $galleryUploadSession,
        GalleryImageProcessor $processor,
    ): JsonResponse {
        $this->ensureActiveSession($client, $galleryUploadSession);

        $validated = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:'.config('gallery.batch_files')],
            'files.*' => [
                'required',
                'file',
                'max:'.config('gallery.max_file_kilobytes'),
                'mimetypes:image/jpeg,image/png,image/webp',
            ],
            'fingerprints' => ['required', 'array', 'min:1', 'max:'.config('gallery.batch_files')],
            'fingerprints.*' => ['required', 'string', 'size:64', 'distinct'],
        ]);

        $files = array_values($validated['files']);
        $fingerprints = array_values($validated['fingerprints']);

        if (count($files) !== count($fingerprints)) {
            throw ValidationException::withMessages([
                'files' => 'Il batch non corrisponde al manifest della gallery.',
            ]);
        }

        $batchBytes = array_sum(array_map(
            fn (UploadedFile $file) => (int) $file->getSize(),
            $files,
        ));

        if ($batchBytes > (int) config('gallery.batch_bytes')) {
            throw ValidationException::withMessages([
                'files' => 'Il batch supera il limite complessivo di 32 MB.',
            ]);
        }

        $items = $galleryUploadSession->items()
            ->whereIn('fingerprint', $fingerprints)
            ->get()
            ->keyBy('fingerprint');

        if ($items->count() !== count(array_unique($fingerprints))) {
            throw ValidationException::withMessages([
                'files' => 'Uno o più file non appartengono al manifest della sessione.',
            ]);
        }

        $staged = [];

        try {
            foreach ($files as $index => $file) {
                $fingerprint = $fingerprints[$index];
                $item = $items->get($fingerprint);

                if ($item->status === 'uploaded') {
                    continue;
                }

                if ((int) $file->getSize() !== $item->byte_size) {
                    throw ValidationException::withMessages([
                        'files' => "Il file {$item->original_name} non coincide con quello selezionato inizialmente.",
                    ]);
                }

                $dimensions = @getimagesize($file->getRealPath());
                if (! $dimensions || ((int) $dimensions[0] * (int) $dimensions[1]) > (int) config('gallery.max_pixels')) {
                    throw ValidationException::withMessages([
                        'files' => "Il file {$item->original_name} non è valido o supera 30 megapixel.",
                    ]);
                }

                $processed = $processor->stage($file, $galleryUploadSession->uuid);
                $staged[] = compact('item', 'processed');
            }

            DB::transaction(function () use ($staged, $galleryUploadSession) {
                foreach ($staged as ['item' => $item, 'processed' => $processed]) {
                    $updated = GalleryUploadItem::query()
                        ->whereKey($item->getKey())
                        ->where('status', 'pending')
                        ->update([
                            ...$processed,
                            'status' => 'uploaded',
                            'updated_at' => now(),
                        ]);

                    if (! $updated) {
                        throw new \RuntimeException('Il batch è stato modificato da un’altra richiesta.');
                    }
                }

                $galleryUploadSession->update([
                    'uploaded_files' => $galleryUploadSession->items()->where('status', 'uploaded')->count(),
                    'uploaded_bytes' => (int) $galleryUploadSession->items()
                        ->where('status', 'uploaded')
                        ->sum('byte_size'),
                    'expires_at' => now()->addDays((int) config('gallery.expires_days')),
                ]);
            });
        } catch (Throwable $exception) {
            foreach ($staged as ['processed' => $processed]) {
                $processor->deleteStaged(
                    $processed['staged_path'],
                    $processed['staged_thumbnail_path'],
                );
            }

            throw $exception;
        }

        return response()->json($this->sessionPayload($galleryUploadSession->fresh()));
    }

    public function complete(
        Client $client,
        GalleryUploadSession $galleryUploadSession,
        GalleryImageProcessor $processor,
        ClientWorkNotifier $notifier,
    ): JsonResponse {
        $this->ensureSessionBelongsToClient($client, $galleryUploadSession);

        if ($galleryUploadSession->status === 'completed') {
            return $this->completedResponse($galleryUploadSession, $notifier);
        }

        if ($galleryUploadSession->status === 'finalizing') {
            return response()->json(['message' => 'La gallery è già in fase di finalizzazione.'], 409);
        }

        $this->ensureActiveSession($client, $galleryUploadSession);
        $galleryUploadSession->refresh();

        if ($galleryUploadSession->uploaded_files !== $galleryUploadSession->expected_files) {
            return response()->json([
                'message' => 'La gallery non è ancora completa.',
                ...$this->sessionPayload($galleryUploadSession),
            ], 422);
        }

        $claimed = GalleryUploadSession::query()
            ->whereKey($galleryUploadSession->getKey())
            ->where('status', 'active')
            ->update(['status' => 'finalizing', 'updated_at' => now()]);

        if (! $claimed) {
            return response()->json(['message' => 'La gallery è già in fase di finalizzazione.'], 409);
        }

        $galleryUploadSession->status = 'finalizing';

        $items = $galleryUploadSession->items()->get();
        $promotions = [];

        try {
            foreach ($items as $item) {
                $staged = [
                    'staged_path' => $item->staged_path,
                    'staged_thumbnail_path' => $item->staged_thumbnail_path,
                ];
                $promotions[] = [
                    'item' => $item,
                    'staged' => $staged,
                    'promoted' => $processor->promote($staged, $client->getKey()),
                ];
            }

            DB::transaction(function () use ($client, $galleryUploadSession, $promotions) {
                $nextOrder = ((int) $client->images()->max('sort_order')) + 1;
                $now = now();

                collect($promotions)->chunk(250)->each(function ($chunk) use ($client, &$nextOrder, $now) {
                    ClientImage::insert($chunk->map(function (array $promotion) use ($client, &$nextOrder, $now) {
                        $item = $promotion['item'];

                        return [
                            'client_id' => $client->getKey(),
                            'image_path' => $promotion['promoted']['image_path'],
                            'thumbnail_path' => $promotion['promoted']['thumbnail_path'],
                            'alt_text' => $client->name,
                            'original_name' => $item->original_name,
                            'byte_size' => $item->byte_size,
                            'width' => $item->width,
                            'height' => $item->height,
                            'sort_order' => $nextOrder++,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    })->all());
                });

                $galleryUploadSession->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'expires_at' => now()->addDays((int) config('gallery.expires_days')),
                ]);
                $galleryUploadSession->items()->delete();
            });
        } catch (Throwable $exception) {
            foreach (array_reverse($promotions) as $promotion) {
                $processor->rollbackPromotion($promotion['promoted'], $promotion['staged']);
            }

            $galleryUploadSession->update(['status' => 'active']);

            throw $exception;
        }

        return $this->completedResponse($galleryUploadSession->fresh(), $notifier);
    }

    public function destroy(
        Client $client,
        GalleryUploadSession $galleryUploadSession,
        GalleryImageProcessor $processor,
    ): JsonResponse {
        $this->ensureSessionBelongsToClient($client, $galleryUploadSession);

        if ($galleryUploadSession->status === 'completed') {
            return response()->json(['message' => 'Una gallery completata non può essere annullata.'], 422);
        }

        foreach ($galleryUploadSession->items as $item) {
            $processor->deleteStaged($item->staged_path, $item->staged_thumbnail_path);
        }

        $galleryUploadSession->update(['status' => 'cancelled']);
        $galleryUploadSession->items()->delete();

        return response()->json(['message' => 'Caricamento annullato.']);
    }

    private function completedResponse(
        GalleryUploadSession $session,
        ClientWorkNotifier $notifier,
    ): JsonResponse {
        $notificationError = null;

        if ($session->notification_requested && ! $session->notification_sent_at) {
            try {
                $notifier->send($session->client);
                $session->update(['notification_sent_at' => now()]);
            } catch (Throwable $exception) {
                report($exception);
                $notificationError = 'Gallery completata, ma l’email non è stata inviata: '.$exception->getMessage();
            }
        }

        return response()->json([
            ...$this->sessionPayload($session->fresh()),
            'redirect_url' => route('admin.clients.edit', $session->client),
            'notification_error' => $notificationError,
        ]);
    }

    private function sessionPayload(GalleryUploadSession $session): array
    {
        $uploadedFingerprints = $session->status === 'active'
            ? $session->items()->where('status', 'uploaded')->pluck('fingerprint')->all()
            : [];

        return [
            'uuid' => $session->uuid,
            'status' => $session->status,
            'expected_files' => $session->expected_files,
            'uploaded_files' => $session->uploaded_files,
            'expected_bytes' => $session->expected_bytes,
            'uploaded_bytes' => $session->uploaded_bytes,
            'uploaded_fingerprints' => $uploadedFingerprints,
            'expires_at' => $session->expires_at?->toIso8601String(),
            'batch_url' => route('admin.clients.gallery-uploads.batch', [$session->client, $session]),
            'complete_url' => route('admin.clients.gallery-uploads.complete', [$session->client, $session]),
            'cancel_url' => route('admin.clients.gallery-uploads.destroy', [$session->client, $session]),
        ];
    }

    private function ensureSessionBelongsToClient(Client $client, GalleryUploadSession $session): void
    {
        abort_unless($session->client_id === $client->getKey(), 404);
    }

    private function ensureActiveSession(Client $client, GalleryUploadSession $session): void
    {
        $this->ensureSessionBelongsToClient($client, $session);
        abort_if($session->status !== 'active' || $session->expires_at->isPast(), 410, 'Sessione di upload scaduta.');
    }

    private function ensureDiskSpace(int $requiredBytes): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $freeBytes = @disk_free_space(storage_path('app'));
        $reserve = (int) config('gallery.min_free_bytes');
        $reservedByActiveSessions = (int) GalleryUploadSession::query()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->selectRaw('COALESCE(SUM(expected_bytes - uploaded_bytes), 0) AS reserved_bytes')
            ->value('reserved_bytes');

        if ($freeBytes !== false && ($freeBytes - $reservedByActiveSessions - $requiredBytes) < $reserve) {
            throw ValidationException::withMessages([
                'manifest' => 'Spazio disco insufficiente per questa gallery. Libera spazio prima di continuare.',
            ]);
        }
    }
}
