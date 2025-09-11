<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{

    protected $fillable = ['category_id','locale','name','slug','link_url','description','seo_title','seo_description','seo_keywords','seo_json'];
    protected $casts = ['seo_json'=>'array'];

    public function category(){ return $this->belongsTo(Category::class); }
}
