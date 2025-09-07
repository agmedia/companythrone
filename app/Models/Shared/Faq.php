<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{

    protected $fillable = ['locale'];


    public function items()
    {
        return $this->hasMany(FaqItem::class);
    }
}