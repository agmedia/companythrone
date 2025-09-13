<?php

namespace App\Models\Admin\Catalog;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

// ✅ import Spatie's Media model (alias to avoid namespace confusion)
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Spatie\Image\Enums\Fit; // for ->fit(Fit::Crop, ...)

class Category extends Model implements HasMedia
{
    use NodeTrait, InteractsWithMedia;
    
    protected $fillable = [
        'group','is_active','is_navbar','position','parent_id',
    ];
    
    protected $casts = [
        'is_active' => 'bool',
        'is_navbar' => 'bool',
        'position'  => 'int',
    ];
    
    public function translations()
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
    
    public function scopeForGroup($query, string $group)
    {
        return $query->where('group', $group)->defaultOrder();
    }
    
    // Media Library
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('icon')->singleFile();
        $this->addMediaCollection('banner')->singleFile();
    }
    
    // ✅ correct signature
    public function registerMediaConversions(?SpatieMedia $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 64, 64)
            ->nonQueued(); // in dev, generate immediately
    }
}
