<?php

use App\Livewire\Back\CategoriesTree;
use App\Livewire\Back\CompaniesIndex;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\Front\{ClickRedirectController, CompanyListController, HomeController, CompanyController, CategoryController};

/**
 *
 * ADMIN (NELokalizirano) – sve pod /admin
 *
 */
Route::prefix('admin')->middleware(['auth','role:master|admin'])->group(function () {
    // Dashboard (Blade wrapper)
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Settings (Blade wrapperi mountaju Livewire komponente)
    Route::view('/settings/profile',    'back.settings.profile')->name('settings.profile');
    Route::view('/settings/password',   'back.settings.password')->name('settings.password');
    Route::view('/settings/appearance', 'back.settings.appearance')->name('settings.appearance');

    // Admin Livewire stranice
    Route::get('/companies',  CompaniesIndex::class)->name('admin.companies');
    Route::get('/categories', CategoriesTree::class)->name('admin.categories');
});

/**
 *
 * PUBLIC (lokalizirano)
 *
 */
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
        \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
        \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
    ],
], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // /{locale}/companies (lista)
    Route::get('/companies', [CompanyListController::class, 'index'])->name('companies.index');

    // /{locale}/companies/{slug} (detalj po lokaliziranom slug-u)
    Route::get('/companies/{companyBySlug}', [CompanyListController::class, 'show'])->name('companies.show');
    Route::get('/categories/{categoryBySlug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::get('/add-company',  [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/add-company', [CompanyController::class, 'store'])->name('companies.store');
});

/**
 *
 */
Route::get('/plan', function () {
    return view('plan');
})->name('plan');
/**
 *
 */

// Signed redirect (izvan LL)
Route::get('/r/{from}/{to}/{slot}', [ClickRedirectController::class, 'go'])
     ->name('click.redirect')->middleware('signed');


require __DIR__.'/auth.php';
