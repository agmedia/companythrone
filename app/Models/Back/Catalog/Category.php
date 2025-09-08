<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{

    use NodeTrait, Sluggable, InteractsWithMedia, HasFactory;

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


    public function registerMediaCollections(): void
    {
        // možeš koristiti 'image' ili 'icon' – ja sam ostavio oba
        $this->addMediaCollection('icon')->singleFile();
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('banner')->singleFile();
    }
}
