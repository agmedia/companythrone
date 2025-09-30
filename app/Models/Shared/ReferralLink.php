<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class ReferralLink extends Model
{

    protected $fillable = ['user_id', 'url', 'label', 'clicks'];


    public function clicks()
    {
        return $this->hasMany(\App\Models\Shared\Click::class, 'from_company_id');
    }
}
