<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{

    protected $fillable = ['number', 'description', 'rotated_at'];

    protected $casts = ['is_active' => 'boolean'];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }


    public function getLabelAttribute(): string
    {
        return $this->title ?? $this->name ?? ('Level #'.$this->id);
    }
}