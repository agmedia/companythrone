<?php

use App\Livewire\Back\CategoriesTree;
use App\Livewire\Back\CompaniesIndex;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\Admin\Settings\Local\CurrencyPageController;
use App\Http\Controllers\Api\V1\Settings\CurrencyController as ApiCurrencyController;
use App\Http\Controllers\Admin\Settings\Local\GeozonePageController;
use App\Http\Controllers\Api\V1\Settings\GeozoneController as ApiGeozoneController;
use App\Http\Controllers\Admin\Settings\Local\LanguagePageController;
use App\Http\Controllers\Api\V1\Settings\LanguageController as ApiLanguageController;
use App\Http\Controllers\Admin\Settings\Local\PaymentsPageController;
use App\Http\Controllers\Api\V1\Settings\PaymentsController as ApiPaymentsController;
use App\Http\Controllers\Admin\Settings\Local\ShippingPageController;
use App\Http\Controllers\Api\V1\Settings\ShippingController as ApiShippingController;
use App\Http\Controllers\Admin\Settings\Local\TaxPageController;
use App\Http\Controllers\Api\V1\Settings\TaxController as ApiTaxController;
use App\Http\Controllers\Admin\Settings\Local\OrderStatusPageController;
use App\Http\Controllers\Api\V1\Settings\OrderStatusController as ApiOrderStatusController;

use App\Http\Controllers\Admin\{BannerEventController,
    CompanyController as AdminCompanyController,
    BannerController as AdminBannerController,
    CategoryController as AdminCategoryController,
    DashboardController,
    Settings\SettingsController,
    SubscriptionController as AdminSubscriptionController,
    UserController};
use App\Http\Controllers\Front\{Account\DashboardController as AccDashboard, Account\LinksController, Account\ProfileController, Account\SubscriptionsController, ClickRedirectController, CompanyListController, HomeController, CompanyController, CategoryController};
use Illuminate\Support\Facades\Crypt;


Route::get('/dashboard', function () {
    // prazan endpoint – RedirectByRole middleware odlučuje
})->name('dashboard')->middleware(['auth','verified','redirect.by.role']);

Route::get('/narudzba', [CompanyController::class, 'order'])->name('order');

/**
 *
 * ADMIN (NELokalizirano) – sve pod /admin
 *
 */
Route::prefix('admin')->middleware(['auth','role:master|admin'])->group(function () {
    // Dashboard (Blade wrapper)
    //Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Tools (header dropdown actions)
    Route::post('/tools/maintenance/on',  [DashboardController::class, 'maintenanceOn'])->name('tools.maintenance.on');
    Route::post('/tools/maintenance/off', [DashboardController::class, 'maintenanceOff'])->name('tools.maintenance.off');
    Route::post('/tools/cache/clear',     [DashboardController::class, 'clearCache'])->name('tools.cache.clear');


    Route::prefix('catalog')->as('catalog.')->group(function () {
        Route::resource('categories', AdminCategoryController::class)->names('categories');
        Route::resource('companies', AdminCompanyController::class)->names('companies');
    });

    Route::resource('subscriptions', AdminSubscriptionController::class)->only(['index','show','edit','update'])->names('subscriptions');
    Route::patch('subscriptions/{subscription}/activate', [AdminSubscriptionController::class, 'activate'])->name('subscriptions.activate');
    Route::patch('subscriptions/{subscription}/pause',    [AdminSubscriptionController::class, 'pause'])->name('subscriptions.pause');
    Route::patch('subscriptions/{subscription}/resume',   [AdminSubscriptionController::class, 'resume'])->name('subscriptions.resume');
    Route::patch('subscriptions/{subscription}/cancel',   [AdminSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    Route::resource('banners', AdminBannerController::class)->names('banners');
    // FullCalendar JSON feed + CRUD
    Route::get('banners/{banner}/events', [BannerEventController::class, 'index'])->name('banners.events.index');
    Route::post('banners/{banner}/events', [BannerEventController::class, 'store'])->name('banners.events.store');
    Route::patch('banners/{banner}/events/{event}', [BannerEventController::class, 'update'])->name('banners.events.update');
    Route::delete('banners/{banner}/events/{event}', [BannerEventController::class, 'destroy'])->name('banners.events.destroy');

    Route::resource('users', UserController::class)->names('users');

    Route::get('app/settings',  [SettingsController::class, 'index'])->name('app.settings.index');
    Route::post('app/settings', [SettingsController::class, 'update'])->name('app.settings.update');

    //
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('currencies', [CurrencyPageController::class, 'index'])->name('currencies.index');
        Route::get('languages', [LanguagePageController::class, 'index'])->name('languages.index');
        Route::get('taxes', [TaxPageController::class, 'index'])->name('taxes.index');
        Route::get('statuses', [OrderStatusPageController::class, 'index'])->name('statuses.index');

        Route::get('payments', [PaymentsPageController::class, 'index'])->name('payments.index');
        Route::get('shipping', [ShippingPageController::class, 'index'])->name('shipping.index');

        Route::get('geozones',            [GeozonePageController::class, 'index'])->name('geozones.index');
        Route::get('geozones/edit/{id?}', [GeozonePageController::class, 'edit'])->name('geozones.edit');

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

    /*Route::resource('banners', AdminBannerController::class)
         ->parameters(['banners' => 'banner']);*/
});

Route::post('/companies/check-unique', [CompanyController::class, 'checkUnique'])
     ->name('companies.checkUnique');

/**
 *
 */
Route::middleware(['web', 'auth'])->prefix('api/v1/settings')->name('api.v1.settings.')->group(function () {
    Route::post('currencies',       [ApiCurrencyController::class, 'store'])->name('currencies.store');
    Route::post('currencies/main',  [ApiCurrencyController::class, 'storeMain'])->name('currencies.storeMain');
    Route::delete('currencies',     [ApiCurrencyController::class, 'destroy'])->name('currencies.destroy');

    Route::post('languages',   [ApiLanguageController::class, 'store'])->name('languages.store');
    Route::delete('languages', [ApiLanguageController::class, 'destroy'])->name('languages.destroy');

    Route::post('taxes',   [ApiTaxController::class, 'store'])->name('taxes.store');
    Route::delete('taxes', [ApiTaxController::class, 'destroy'])->name('taxes.destroy');

    Route::post('statuses',   [ApiOrderStatusController::class, 'store'])->name('statuses.store');
    Route::delete('statuses', [ApiOrderStatusController::class, 'destroy'])->name('statuses.destroy');

    Route::post('payments',   [ApiPaymentsController::class, 'store'])->name('payments.store');
    Route::delete('payments', [ApiPaymentsController::class, 'destroy'])->name('payments.destroy');

    Route::post('shipping',   [ApiShippingController::class, 'store'])->name('shipping.store');
    Route::delete('shipping', [ApiShippingController::class, 'destroy'])->name('shipping.destroy');

    Route::post('geozones',   [ApiGeozoneController::class, 'store'])->name('geozones.store');
    Route::delete('geozones', [ApiGeozoneController::class, 'destroy'])->name('geozones.destroy');
});


require __DIR__.'/auth.php';

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
    Route::get('/companies', [CompanyListController::class, 'index'])->name('companies.index');
    Route::get('/companies/{companyBySlug}', [CompanyListController::class, 'show'])->name('companies.show');

    Route::get('/dodaj-tvrtku',  [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/add-company', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/nacin-placanja', [CompanyController::class, 'payment'])->name('companies.payment');
    Route::post('/review',  [CompanyController::class, 'review'])->name('companies.review');
    Route::get('/uspjeh',  [CompanyController::class, 'success'])->name('companies.success');
    Route::get('/greska',  [CompanyController::class, 'error'])->name('companies.error');

    /**
     * Mini-admin za vlasnike
     */
    Route::middleware(['auth','verified'])
         ->prefix('moj-racun')
         ->as('account.')
         ->group(function () {
             Route::get('/', [AccDashboard::class, 'index'])->name('dashboard');   // = route('account.dashboard')

             Route::get('/links',  [LinksController::class, 'index'])->name('links.index');
             Route::post('/links', [LinksController::class, 'store'])->name('links.store');

             Route::get('/payments', [SubscriptionsController::class, 'index'])->name('payments');
             Route::get('/subscriptions', [SubscriptionsController::class, 'subscriptions'])->name('subscriptions');
             Route::get('/invoices',      [SubscriptionsController::class, 'invoices'])->name('invoices');
             Route::get('/moj-racun/invoices/{invoice}/download', [SubscriptionsController::class, 'downloadInvoice'])
                  ->name('account.invoices.download');

             Route::get('company', [\App\Http\Controllers\Front\Account\CompanyController::class, 'edit'])
                  ->name('company.edit');
             Route::put('company', [\App\Http\Controllers\Front\Account\CompanyController::class, 'update'])
                  ->name('company.update');

             Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
             Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
         });

    Route::post('/account/links/click', [LinksController::class, 'click'])
         ->name('account.links.click')
         ->middleware('auth');

    // složi regex iz svih lokaliziranih slugova
    $allowedGroupSlugs = collect(config('settings.group_slugs', []))
        ->flatMap(fn ($perLocale) => array_values($perLocale))
        ->map(fn ($s) => preg_quote($s, '#'))
        ->unique()
        ->implode('|');
    // Root listing grupe (npr. /hr/tvrtke, /hr/blog, /hr/info-stranica)
    Route::get('{groupSlug}', [CategoryController::class, 'index'])
         ->where('groupSlug', $allowedGroupSlugs)
         ->name('front.group.index');
    // Pojedinačna kategorija / stranica (npr. /hr/tvrtke/auto, /hr/info-stranica/o-nama)
    Route::get('{groupSlug}/{slug}', [CategoryController::class, 'show'])
         ->where('groupSlug', $allowedGroupSlugs)
         ->name('front.category.show');


    Route::get('/decrypt-email', function (Illuminate\Http\Request $request) {
        return response()->json([
            'email' => Crypt::decryptString($request->get('data'))
        ]);
    });

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






