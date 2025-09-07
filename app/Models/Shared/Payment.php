<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = ['company_id', 'amount', 'status', 'period_start', 'period_end', 'issued_at', 'paid_at', 'provider', 'provider_ref'];


    public function company()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class);
    }
}
