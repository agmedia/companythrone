<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Company extends Model implements HasMedia
{

    use InteractsWithMedia, Sluggable, HasFactory;

    protected $fillable = [
        'level_id', 'name', 'slug', 'oib', 'street', 'street_no', 'city', 'state', 'email', 'weburl', 'phone', 'is_published', 'is_link_active', 'referrals_count', 'published_at'
    ];

    protected $casts = [
        'is_published'   => 'bool',
        'is_link_active' => 'bool',
        'published_at'   => 'datetime',
    ];


    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }


    public function translations(): HasMany
    {
        return $this->hasMany(CompanyTranslation::class, 'company_id');
    }


    public function translation(?string $locale = null): ?CompanyTranslation
    {
        $locale   = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale');

        return $this->translations->firstWhere('locale', $locale)
               ?? $this->translations->firstWhere('locale', $fallback);
    }


    public function translated(string $field, ?string $locale = null, $fallback = null)
    {
        $loc = $locale ?: app()->getLocale();
        $t   = $this->relations['translation_' . $loc] ??= $this->translations->firstWhere('locale', $loc);

        return $t?->{$field} ?? $fallback;
    }


    // sugar accessor-i
    public function getTNameAttribute()
    {
        return $this->translated('name', null, '[no name]');
    }


    public function getTSloganAttribute()
    {
        return $this->translated('slogan');
    }


    public function getTDescAttribute()
    {
        return $this->translated('description');
    }


    public function getTSlugAttribute()
    {
        return $this->translated('slug');
    }


    public function level()
    {
        return $this->belongsTo(Level::class);
    }


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_company');
    }


    public function payments()
    {
        return $this->hasMany(\App\Models\Shared\Payment::class);
    }


    public function clicks()
    {
        return $this->hasMany(\App\Models\Shared\Click::class);
    }


    public function sessions()
    {
        return $this->hasMany(\App\Models\Shared\DailySession::class);
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();   // 1 logo
        $this->addMediaCollection('banner')->singleFile(); // 1 banner
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->fit(Fit::Crop, 128, 128)
             ->nonQueued();

        $this->addMediaConversion('wide') // za banner pregleda
             ->fit(Fit::Crop, 1200, 300)
             ->nonQueued();
    }

}
