<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\ContactRequestController as AdminContactRequestController;
use App\Http\Controllers\Admin\PortfolioProjectController as AdminPortfolioProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

Route::view('/contatti', 'contatti')->name('contatti');
Route::post('/contatti', [ContactRequestController::class, 'store'])->name('contatti.store');
Route::view('/servizi', 'servizi')->name('servizi');
Route::get('/clienti', [ClientController::class, 'index'])->name('clienti');
Route::get('/clienti/{client}', [ClientController::class, 'show'])->name('clienti.show');
Route::get('/', [PortfolioController::class, 'home'])->name('home');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/portfolio/{portfolioProject}', [PortfolioController::class, 'show'])->name('portfolio.show');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/cookie-policy', 'cookie-policy')->name('cookie-policy');
Route::redirect('/login', '/admin/login')->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::redirect('/', '/admin/portfolio')->name('dashboard');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::resource('clients', AdminClientController::class)
            ->parameters(['clients' => 'client'])
            ->except(['show']);
        Route::resource('contact-requests', AdminContactRequestController::class)
            ->only(['index', 'show', 'destroy']);
        Route::resource('portfolio', AdminPortfolioProjectController::class)
            ->parameters(['portfolio' => 'portfolioProject'])
            ->except(['show']);
    });
});
