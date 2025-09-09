<?php

namespace App\Models\Back\Marketing;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Translations\BannerTranslation;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Banner extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $fillable = ['title', 'slogan', 'url', 'status'];


    public function translations(): HasMany
    {
        return $this->hasMany(BannerTranslation::class);
    }


    public function translation(?string $locale = null): ?BannerTranslation
    {
        $locale   = $locale ?: app()->getLocale();
        $fallback = config('app.fallback_locale');

        return $this->translations->firstWhere('locale', $locale)
               ?? $this->translations->firstWhere('locale', $fallback);
    }


    public function schedules()
    {
        return $this->hasMany(BannerSchedule::class);
    }


    public function currentSchedule(?Carbon $date = null): ?BannerSchedule
    {
        $date ??= Carbon::today();

        return $this->schedules()
                    ->forDate($date)
                    ->orderBy('position')
                    ->orderByDesc('start_date')
                    ->first();
    }


    public function getTitleAttribute($value)
    {
        return $this->translation()?->title ?? $value;
    }


    public function getSloganAttribute($value)
    {
        return $this->translation()?->slogan ?? $value;
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
