<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;

class ReminderController extends Controller
{
    public function unsubscribe(User $user, string $token)
    {
        if (sha1($user->email) !== $token) {
            abort(403, 'Nevažeći token.');
        }

        $user->daily_reminder_opt_out = true;
        $user->save();

        return 'Uspješno ste se odjavili s podsjetnika. Nećete više primati ovakve emailove.';
    }
}
