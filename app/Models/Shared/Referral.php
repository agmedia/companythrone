<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{

    protected $table = 'company_referrals';

    protected $fillable = ['referrer_company_id', 'referred_company_id'];


    public function referrer()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class, 'referrer_company_id');
    }


    public function referred()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class, 'referred_company_id');
    }
}
