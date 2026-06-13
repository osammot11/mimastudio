<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use App\Models\PortfolioProjectImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PortfolioProjectController extends Controller
{
    public function index(): View
    {
        $projects = PortfolioProject::query()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('admin.portfolio.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.portfolio.create', [
            'project' => new PortfolioProject([
                'is_published' => true,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedProjectData($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);
        $data['is_published'] = $request->boolean('is_published');
        $data['cover_image'] = $request->file('cover_image')->store('portfolio', 'public');

        $project = PortfolioProject::create($data);
        $this->storeGalleryImages($request, $project);

        return redirect()
            ->route('admin.portfolio.edit', $project)
            ->with('status', 'Progetto creato.');
    }

    public function edit(PortfolioProject $portfolioProject): View
    {
        $portfolioProject->load('images');

        return view('admin.portfolio.edit', [
            'project' => $portfolioProject,
        ]);
    }

    public function update(Request $request, PortfolioProject $portfolioProject): RedirectResponse
    {
        $data = $this->validatedProjectData($request, $portfolioProject);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title'], $portfolioProject);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover_image')) {
            $this->deleteStoredFile($portfolioProject->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('portfolio', 'public');
        }

        $portfolioProject->update($data);
        $this->updateGalleryImages($request, $portfolioProject);
        $this->deleteGalleryImages($request, $portfolioProject);
        $this->storeGalleryImages($request, $portfolioProject);

        return redirect()
            ->route('admin.portfolio.edit', $portfolioProject)
            ->with('status', 'Progetto aggiornato.');
    }

    public function destroy(PortfolioProject $portfolioProject): RedirectResponse
    {
        $portfolioProject->load('images');

        $this->deleteStoredFile($portfolioProject->cover_image);

        foreach ($portfolioProject->images as $image) {
            $this->deleteStoredFile($image->image_path);
        }

        $portfolioProject->delete();

        return redirect()
            ->route('admin.portfolio.index')
            ->with('status', 'Progetto eliminato.');
    }

    private function validatedProjectData(Request $request, ?PortfolioProject $project = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('portfolio_projects', 'slug')->ignore($project),
            ],
            'description' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'cover_image' => [$project ? 'nullable' : 'required', 'image', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:4096'],
            'image_alt' => ['nullable', 'array'],
            'image_alt.*' => ['nullable', 'string', 'max:255'],
            'image_sort_order' => ['nullable', 'array'],
            'image_sort_order.*' => ['nullable', 'integer', 'min:0'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:portfolio_project_images,id'],
        ]);
    }

    private function uniqueSlug(string $source, ?PortfolioProject $project = null): string
    {
        $baseSlug = Str::slug($source);
        $slug = $baseSlug;
        $counter = 2;

        while (PortfolioProject::where('slug', $slug)
            ->when($project, fn ($query) => $query->whereKeyNot($project->getKey()))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function storeGalleryImages(Request $request, PortfolioProject $project): void
    {
        foreach ($request->file('gallery_images', []) as $image) {
            $project->images()->create([
                'image_path' => $image->store('portfolio', 'public'),
                'alt_text' => $project->title,
                'sort_order' => ($project->images()->max('sort_order') ?? 0) + 1,
            ]);
        }
    }

    private function updateGalleryImages(Request $request, PortfolioProject $project): void
    {
        foreach ($request->input('image_alt', []) as $imageId => $altText) {
            $image = $project->images()->whereKey($imageId)->first();

            if (! $image) {
                continue;
            }

            $image->update([
                'alt_text' => $altText,
                'sort_order' => (int) $request->input("image_sort_order.{$imageId}", $image->sort_order),
            ]);
        }
    }

    private function deleteGalleryImages(Request $request, PortfolioProject $project): void
    {
        $images = $project->images()->whereIn('id', $request->input('delete_images', []))->get();

        foreach ($images as $image) {
            $this->deleteStoredFile($image->image_path);
            $image->delete();
        }
    }

    private function deleteStoredFile(?string $path): void
    {
        if (! $path || str_starts_with($path, 'images/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
