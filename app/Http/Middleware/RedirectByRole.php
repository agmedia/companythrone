<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectByRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Ako nije prijavljen, pusti dalje (auth middleware će riješiti)
        if (!$user) {
            return $next($request);
        }

        // Ako smo već na admin ili account dashboardu, pusti dalje (bez petlje)
        if ($request->routeIs('admin.dashboard', 'account.dashboard')) {
            return $next($request);
        }

        // Spatie\Permission?
        if (method_exists($user, 'hasAnyRole')) {
            if ($user->hasAnyRole(['admin', 'master'])) {
                return redirect()->to(route('admin.dashboard'));
            }
            if ($user->hasRole('company_owner')) {
                return redirect()->to(localized_route('account.dashboard'));
            }

            if ($user->hasRole('customer')) {
                if ($user->company) {
                    // ako već ima tvrtku, vodi ga na neku drugu stranicu (npr. account dashboard)
                    return redirect()->to(localized_route('account.dashboard'));
                }
                // ako nema tvrtku, vodi ga na wizard za unos
                return redirect()->to(localized_route('companies.create'));
            }
        }

        // Fallback (ako nema Spatie-a): kolona role na users
        if (isset($user->role)) {
            if (in_array($user->role, ['admin', 'master'], true)) {
                return redirect()->to(route('admin.dashboard'));
            }
            if ($user->role === 'company_owner') {
                return redirect()->to(localized_route('account.dashboard'));
            }
        }

        // Posljednja opcija – na početnu
        return redirect()->to(localized_route('home'));
    }
}
