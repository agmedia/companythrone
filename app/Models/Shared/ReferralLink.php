<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class ReferralLink extends Model
{

    protected $fillable = ['user_id', 'url', 'label', 'title', 'phone','tvrtka', 'clicks'];


    public function clicks()
    {
        return $this->hasMany(\App\Models\Shared\Click::class, 'from_company_id');
    }


    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}
