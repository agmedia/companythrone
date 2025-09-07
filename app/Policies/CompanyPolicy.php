<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Back\Catalog\Company;

class CompanyPolicy
{
    public function view(User $user, Company $company): bool
    {
        return $user->hasRole('admin') || $user->company_id === $company->id;
    }

    public function update(User $user, Company $company): bool
    {
        return $user->hasRole('admin') || $user->hasRole('company_owner') && $user->company_id === $company->id;
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->hasRole('admin');
    }
}
