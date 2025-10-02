<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    public function edit()
    {
        $user = auth()->user();

        return view('front.account.profile', compact('user'));
    }


    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id]
        ]);

        $updated = $user->update($validated);

        if ($updated) {
            $user->detail()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'fname' => $request->input('fname', ''),
                    'lname' => $request->input('lname', ''),
                    'address' => $request->input('address', ''),
                    'zip'     => $request->input('zip', ''),
                    'city'    => $request->input('city', ''),
                    'state'   => $request->input('state', ''),
                    'phone'   => $request->input('phone', ''),
                ]
            );
        }

        return back()->with('status', __('Podaci su uspješno ažurirani.'));
    }
}

