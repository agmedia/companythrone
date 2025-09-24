<?php

use App\Livewire\Back\CategoriesTree;
use App\Livewire\Back\CompaniesIndex;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\Admin\{CompanyController as AdminCompanyController, BannerController as AdminBannerController, CategoryController as AdminCategoryController};
use App\Http\Controllers\Front\{ClickRedirectController, CompanyListController, HomeController, CompanyController, CategoryController};
use Illuminate\Support\Facades\Crypt;

/**
 *
 * ADMIN (NELokalizirano) – sve pod /admin
 *
 */
Route::prefix('admin')->middleware(['auth','role:master|admin'])->group(function () {
    // Dashboard (Blade wrapper)
    Route::view('/dashboard', 'dashboard')->name('dashboard');


    Route::prefix('catalog')->as('catalog.')->group(function () {
        Route::resource('categories', AdminCategoryController::class)->names('categories');
        Route::resource('companies', AdminCompanyController::class)->names('companies');
    });

    // Settings (Blade wrapperi mountaju Livewire komponente)
    Route::view('/settings/profile',    'back.settings.profile')->name('settings.profile');
    Route::view('/settings/password',   'back.settings.password')->name('settings.password');
    Route::view('/settings/appearance', 'back.settings.appearance')->name('settings.appearance');

    Route::view('/settings/company', 'back.settings.company')->name('settings.company');

    // Admin Livewire stranice
    /*Route::get('/companies',  CompaniesIndex::class)->name('admin.companies');
    Route::get('/categories', CategoriesTree::class)->name('admin.categories');*/

    /*Route::resource('companies', AdminCompanyController::class)
         ->parameters(['companies' => 'company']);*/

    Route::resource('banners', AdminBannerController::class)
         ->parameters(['banners' => 'banner']);
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

    Route::get('/kontakt', [HomeController::class, 'contact'])->name('kontakt');

  Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

    // /{locale}/companies (lista)
    Route::get('/companies', [CompanyListController::class, 'index'])->name('companies.index');

    // /{locale}/companies/{slug} (detalj po lokaliziranom slug-u)
    Route::get('/companies/{companyBySlug}', [CompanyListController::class, 'show'])->name('companies.show');
    Route::get('/categories/{categoryBySlug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::get('/decrypt-email', function (Illuminate\Http\Request $request) {
        return response()->json([
            'email' => Crypt::decryptString($request->get('data'))
        ]);
    });

    Route::get('/add-company',  [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/add-company', [CompanyController::class, 'store'])->name('companies.store');


    Route::get('/add-payment',  [CompanyController::class, 'payment'])->name('companies.payment');
    Route::post('/review',  [CompanyController::class, 'review'])->name('companies.review');

    Route::post('/success',  [CompanyController::class, 'success'])->name('companies.success');
});

Route::post('/kontakt/posalji', [HomeController::class, 'sendContactMessage'])->name('poruka');

/**
 *
 *
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


