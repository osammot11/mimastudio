<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\ContactRequestController as AdminContactRequestController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\PortfolioProjectController as AdminPortfolioProjectController;
use App\Http\Controllers\Admin\WorkDeliveryController as AdminWorkDeliveryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientPortalController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SiteAccessController;
use Illuminate\Support\Facades\Route;

Route::get('/accesso-sito', [SiteAccessController::class, 'show'])->name('site-access.show');
Route::post('/accesso-sito', [SiteAccessController::class, 'authenticate'])
    ->middleware('throttle:10,1')
    ->name('site-access.authenticate');

Route::middleware('site.access')->group(function () {
    Route::view('/contatti', 'contatti')->name('contatti');
    Route::post('/contatti', [ContactRequestController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('contatti.store');
    Route::view('/servizi', 'servizi')->name('servizi');
    Route::get('/clienti', [ClientController::class, 'index'])->name('clienti');
    Route::get('/clienti/{client}', [ClientController::class, 'show'])->name('clienti.show');
    Route::get('/', [PortfolioController::class, 'home'])->name('home');
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
    Route::get('/portfolio/{portfolioProject}', [PortfolioController::class, 'show'])->name('portfolio.show');
    Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
    Route::view('/cookie-policy', 'cookie-policy')->name('cookie-policy');
});

Route::redirect('/login', '/admin/login')->name('login');

Route::prefix('area-clienti')->name('client-area.')->group(function () {
    Route::get('/', [ClientPortalController::class, 'login'])->name('login');
    Route::post('/link-accesso', [ClientPortalController::class, 'sendAccessLink'])
        ->middleware('throttle:5,1')
        ->name('send-link');
    Route::get('/accesso', [ClientPortalController::class, 'authenticate'])
        ->middleware('signed')
        ->name('authenticate');

    Route::middleware('client.portal')->group(function () {
        Route::get('/lavori', [ClientPortalController::class, 'index'])->name('index');
        Route::get('/gallerie/{client}', [ClientPortalController::class, 'showClient'])->name('clients.show');
        Route::get('/lavori/{workDelivery}', [ClientPortalController::class, 'show'])->name('show');
        Route::post('/logout', [ClientPortalController::class, 'logout'])->name('logout');
    });
});

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
        Route::get('customer-access-links', [AdminCustomerController::class, 'accessLinks'])
            ->name('customer-access-links.index');
        Route::resource('customers', AdminCustomerController::class)
            ->except(['show', 'destroy']);
        Route::resource('contact-requests', AdminContactRequestController::class)
            ->only(['index', 'show', 'destroy']);
        Route::post('work-deliveries/{workDelivery}/resend', [AdminWorkDeliveryController::class, 'resend'])
            ->name('work-deliveries.resend');
        Route::resource('work-deliveries', AdminWorkDeliveryController::class)
            ->parameters(['work-deliveries' => 'workDelivery'])
            ->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::resource('portfolio', AdminPortfolioProjectController::class)
            ->parameters(['portfolio' => 'portfolioProject'])
            ->except(['show']);
    });
});
