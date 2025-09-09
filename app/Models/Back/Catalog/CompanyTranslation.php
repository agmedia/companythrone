<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Model;

class CompanyTranslation extends Model
{
    protected $fillable = ['company_id','locale','name','slug','slogan','description'];
}
