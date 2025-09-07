<?php

namespace App\Policies;

use App\Models\User;

class BannerPolicy
{

    public function manage(User $u): bool
    {
        return $u->hasRole('admin');
    }
}
