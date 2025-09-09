<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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


    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }


    public function translation(?string $locale = null): ?CategoryTranslation
    {
        $locale   = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale');

        return $this->translations->firstWhere('locale', $locale)
               ?? $this->translations->firstWhere('locale', $fallback);
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


    public function getNameAttribute()
    {
        return $this->translations->firstWhere('locale', app()->getLocale())?->name;
    }


    public function getSlugAttribute()
    {
        return $this->translations->firstWhere('locale', app()->getLocale())?->slug;
    }


    public function getDescAttribute()
    {
        return $this->translations->firstWhere('locale', app()->getLocale())?->description;
    }
}
