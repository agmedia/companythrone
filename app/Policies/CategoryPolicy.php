<?php

namespace App\Policies;

use App\Models\User;

class CategoryPolicy
{

    public function manage(User $u): bool
    {
        return $u->hasRole('admin');
    }
}
