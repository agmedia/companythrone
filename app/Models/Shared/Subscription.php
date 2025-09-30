<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{

    protected $fillable = [
        'company_id', 'plan', 'period', 'price', 'currency',
        'status', 'is_auto_renew', 'starts_on', 'ends_on',
        'next_renewal_on', 'trial_ends_on', 'canceled_at', 'notes'
    ];

    protected $casts = [
        'is_auto_renew'   => 'boolean',
        'starts_on'       => 'date',
        'ends_on'         => 'date',
        'next_renewal_on' => 'date',
        'trial_ends_on'   => 'date',
        'canceled_at'     => 'datetime',
    ];


    // 🔹 Scope za aktivne pretplate
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
                     ->where(function ($q) {
                         $q->whereNull('ends_on')
                           ->orWhere('ends_on', '>=', now());
                     });
    }


    // 🔹 Scope za trial
    public function scopeTrialing(Builder $query): Builder
    {
        return $query->where('status', 'trialing');
    }


    // 🔹 Scope za otkazane
    public function scopeCanceled(Builder $query): Builder
    {
        return $query->where('status', 'canceled');
    }


    // 🔹 Relacija
    public function company()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class);
    }
}
