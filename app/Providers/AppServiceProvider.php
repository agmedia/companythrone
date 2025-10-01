<?php

namespace App\Providers;

use App\Models\Back\Catalog\Category;
use App\Models\Back\Catalog\Company;
use App\Policies\CategoryPolicy;
use App\Policies\CompanyPolicy;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        Company::class  => CompanyPolicy::class,
        Category::class => CategoryPolicy::class,
        //Banner::class   => BannerPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $user = Auth::user();
            $hasCompany = $user ? Company::where('user_id', $user->id)->exists() : false;
            $view->with('hasCompany', $hasCompany);
        });
    }
}
