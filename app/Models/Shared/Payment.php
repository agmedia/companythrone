<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Payment extends Model
{

    protected $fillable = [
        'company_id', 'subscription_id', 'amount', 'currency', 'status',
        'period_start', 'period_end', 'issued_at', 'paid_at',
        'provider', 'provider_ref', 'method', 'invoice_no', 'vat_rate', 'tax_amount', 'net_amount', 'meta'
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'issued_at'    => 'datetime',
        'paid_at'      => 'datetime',
        'meta'         => 'array',
    ];


    // 🔹 Scope za plaćene uplate
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }


    // 🔹 Scope za neuspješne uplate
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }


    // 🔹 Scope za pending
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }


    // 🔹 Relacija
    public function company()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class);
    }


    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
