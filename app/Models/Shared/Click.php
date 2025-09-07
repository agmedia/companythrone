<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Click extends Model
{

    protected $fillable = ['company_id', 'from_company_id', 'day', 'slot', 'link_url', 'ip', 'user_agent'];


    public function company()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class, 'company_id');
    }


    public function fromCompany()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class, 'from_company_id');
    }
}
