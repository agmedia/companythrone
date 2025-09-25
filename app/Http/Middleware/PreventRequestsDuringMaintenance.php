<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * URIs koje su izuzete iz maintenance moda.
     */
    protected $except = [
        // Auth
        'login',
        'register',
        'logout',
        'password/*',
        'two-factor-challenge',
        // Ako koristiš Sanctum/SPA:
        'sanctum/csrf-cookie',

        // Admin
        'admin',
        'admin/*',

        // (Opcionalno) API za admin ako postoji
        'api/admin/*',
    ];
}
