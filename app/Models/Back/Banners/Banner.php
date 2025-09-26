<?php

namespace App\Models\Back\Banners;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;

class Banner extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'banners';
    protected $guarded = ['id'];
    protected $casts = ['clicks' => 'integer'];

    public function translations(): HasMany
    {
        return $this->hasMany(BannerTranslation::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BannerSchedule::class);
    }

    // Helper: vrati prijevod za zadani (ili current) locale
    public function translation(?string $locale = null): ?BannerTranslation
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $t = $this->relations['translations'] ?? null;
        if ($t) return $t->firstWhere('locale', $locale) ?: $t->first(); // fallback na prvi
        return $this->translations()->where('locale', $locale)->first()
            ?: $this->translations()->first();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner')->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit(Fit::Crop, 256, 128)->nonQueued();
        $this->addMediaConversion('wide')->fit(Fit::Crop, 1200, 400)->nonQueued();
    }
}
