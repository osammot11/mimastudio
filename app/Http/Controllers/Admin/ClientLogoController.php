<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientLogo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ClientLogoController extends Controller
{
    public function index(): View
    {
        $clientLogos = ClientLogo::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.client-logos.index', compact('clientLogos'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'logos' => ['required', 'array', 'min:1', 'max:1000'],
            'logos.*' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $storedPaths = [];

        try {
            $clientLogos = DB::transaction(function () use ($validated, &$storedPaths): Collection {
                $nextSortOrder = ((int) ClientLogo::query()->max('sort_order')) + 1;

                return collect($validated['logos'])->map(function ($file, int $index) use (&$storedPaths, $nextSortOrder): ClientLogo {
                    $path = $file->store('client-logos', 'public');
                    $storedPaths[] = $path;

                    return ClientLogo::create([
                        'name' => Str::headline(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                        'image_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'sort_order' => $nextSortOrder + $index,
                    ]);
                });
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $clientLogos->count() === 1 ? 'Logo caricato.' : 'Loghi caricati.',
                'logos' => $clientLogos->map(fn (ClientLogo $logo): array => [
                    'id' => $logo->id,
                    'name' => $logo->name,
                    'image_url' => $logo->imageUrl(),
                    'sort_order' => $logo->sort_order,
                ]),
            ], 201);
        }

        return redirect()
            ->route('admin.client-logos.index')
            ->with('status', $clientLogos->count().' loghi caricati.');
    }

    public function update(Request $request, ClientLogo $clientLogo): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $oldPath = $clientLogo->image_path;
        $newPath = null;
        unset($validated['logo']);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $newPath = $file->store('client-logos', 'public');
            $validated['image_path'] = $newPath;
            $validated['original_name'] = $file->getClientOriginalName();
        }

        try {
            $clientLogo->update($validated);
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()
            ->route('admin.client-logos.index')
            ->with('status', 'Logo aggiornato.');
    }

    public function destroy(ClientLogo $clientLogo): RedirectResponse
    {
        $path = $clientLogo->image_path;
        $clientLogo->delete();
        Storage::disk('public')->delete($path);

        return redirect()
            ->route('admin.client-logos.index')
            ->with('status', 'Logo eliminato.');
    }
}
