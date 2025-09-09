<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{

    protected $fillable = ['category_id','locale','name','slug','description'];
}
