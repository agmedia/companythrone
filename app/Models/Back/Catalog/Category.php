<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{

    use NodeTrait, Sluggable;

    protected $fillable = ['name', 'slug', 'parent_id'];


    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }


    // ⬇️ Fix trait conflict: override replicate and call base Eloquent implementation
    public function replicate(array $except = null)
    {
        $copy = parent::replicate($except ?? []);

        // Reset NestedSet fields so the clone is treated as a fresh node
        $copy->setAttribute($this->getLftName(), null);
        $copy->setAttribute($this->getRgtName(), null);
        $copy->setAttribute($this->getParentIdName(), null);

        // Force slug regeneration on save
        $copy->slug = null;

        return $copy;
    }


    public function companies()
    {
        return $this->belongsToMany(Company::class, 'category_company');
    }
}
