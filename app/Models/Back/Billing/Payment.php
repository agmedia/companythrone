<?php

namespace App\Models\Back\Billing;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'company_id','subscription_id','amount','currency','vat_rate','tax_amount','net_amount',
        'status','period_start','period_end','issued_at','paid_at',
        'provider','provider_ref','method','invoice_no','meta',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'vat_rate'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'net_amount'   => 'decimal:2',
        'period_start' => 'date',
        'period_end'   => 'date',
        'issued_at'    => 'datetime',
        'paid_at'      => 'datetime',
        'meta'         => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
