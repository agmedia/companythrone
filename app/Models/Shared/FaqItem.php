<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{

    protected $fillable = ['faq_id', 'question', 'answer', 'sort'];


    public function faq()
    {
        return $this->belongsTo(Faq::class);
    }
}