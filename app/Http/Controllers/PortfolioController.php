<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function home(): View
    {
        $projects = PortfolioProject::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->limit(6)
            ->get();

        return view('home', compact('projects'));
    }

    public function index(Request $request): View
    {
        $categories = PortfolioProject::query()
            ->published()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category')
            ->unique()
            ->values();

        $selectedCategory = $request->query('categoria');
        $activeCategory = $categories->first(fn ($category) => Str::slug($category) === $selectedCategory);

        $projects = PortfolioProject::query()
            ->published()
            ->when($activeCategory, fn ($query) => $query->where('category', $activeCategory))
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('portfolio', compact('projects', 'categories', 'activeCategory'));
    }

    public function show(PortfolioProject $portfolioProject): View
    {
        abort_unless($portfolioProject->is_published, 404);

        $portfolioProject->load('images');

        return view('portfolio-show', [
            'project' => $portfolioProject,
        ]);
    }
}
