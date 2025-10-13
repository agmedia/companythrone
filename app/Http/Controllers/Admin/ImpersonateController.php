<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ImpersonateController extends Controller
{
    public function start(Request $request, User $user)
    {
        $admin = Auth::user();

        // samo master ili admin mogu
        if (! $admin->hasAnyRole(['master', 'admin'])) {
            abort(Response::HTTP_FORBIDDEN, 'Nemate ovlasti za impersonaciju.');
        }

        // Spremi ID admina u session da se može vratiti
        session(['impersonate_original_id' => $admin->id]);

        // Prijavi se kao odabrani korisnik
        Auth::login($user);

        return redirect()->route('dashboard')
                         ->with('status', __('Sada ste prijavljeni kao :name.', ['name' => $user->name]));
    }

    public function stop(Request $request)
    {
        $originalId = session('impersonate_original_id');

        if ($originalId) {
            $admin = \App\Models\User::find($originalId);
            session()->forget('impersonate_original_id');

            Auth::login($admin);

            return redirect()->route('admin.dashboard')
                             ->with('status', __('Vratili ste se u administratorski račun.'));
        }

        return redirect()->route('login');
    }
}
