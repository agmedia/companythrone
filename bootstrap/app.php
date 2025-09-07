<?php

use App\Events\DailySessionCompleted;
use App\Jobs\CloseDayAndActivateLinks;
use App\Jobs\GenerateDailySessions;
use App\Jobs\RotateLevels;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ovdje po potrebi globalni middleware
        //
        //
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            //
            /*'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,*/
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // 00:05 – generiraj dnevne tipke
        $schedule->job(new GenerateDailySessions)->dailyAt('00:05')->onOneServer();

        // 23:55 – zaključi dan i aktiviraj linkove
        $schedule->job(new CloseDayAndActivateLinks)->dailyAt('23:55')->onOneServer();

        // PON & ČET u 03:00 – rotiraj levele
        $schedule->job(new RotateLevels)->weekly()->onOneServer();
    })
   /* ->withEvents(function (Events $events) {
        // Ako tvoj skeleton ima .withEvents (Laravel 12+)
        $events->listen(DailySessionCompleted::class, [ActivateCompanyLink::class, 'handle']);
        $events->listen(SubscriptionExpired::class, [DeactivateCompanyOnExpiry::class, 'handle']);
    })*/
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
