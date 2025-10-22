<?php

namespace App\Models\Back\Billing;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{

    protected $fillable = [
        'company_id', 'plan', 'period', 'price', 'currency',
        'status', 'is_auto_renew',
        'starts_on', 'ends_on', 'next_renewal_on', 'trial_ends_on', 'canceled_at',
        'notes',
    ];

    protected $casts = [
        'is_auto_renew'   => 'boolean',
        'starts_on'       => 'date',
        'ends_on'         => 'date',
        'next_renewal_on' => 'date',
        'trial_ends_on'   => 'date',
        'canceled_at'     => 'datetime',
        'price'           => 'decimal:2',
    ];


    public function company()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class);
    }


    public function payments()
    {
        return $this->hasMany(\App\Models\Back\Billing\Payment::class);
    }


    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    public static function getPrice(int|float|string $payment_price)
    {
        $subscription_price = app_settings()->getPrice();

        return $subscription_price + $payment_price;
    }
}
