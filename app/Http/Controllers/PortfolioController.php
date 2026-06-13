<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
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

    public function index(): View
    {
        $projects = PortfolioProject::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('portfolio', compact('projects'));
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
