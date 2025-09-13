<?php

namespace App\Models\Admin\Catalog;

use Illuminate\Database\Eloquent\Model;


class CategoryTranslation extends Model
{
    public $timestamps = false;
    
    
    protected $fillable = [
        'category_id', 'locale',
        'title', 'slug', 'link_url',
        'description',
        'seo_title', 'seo_description', 'seo_keywords', 'seo_json',
    ];
    
    
    protected $casts = [
        'seo_json' => 'array',
    ];
    
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}