<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{

    protected $fillable = ['number', 'description', 'rotated_at'];


    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}