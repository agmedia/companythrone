<?php

namespace App\Models\Back\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Cviebrock\EloquentSluggable\Sluggable;

class Company extends Model implements HasMedia
{

    use InteractsWithMedia, Sluggable, HasFactory;

    protected $fillable = [
        'level_id', 'name', 'slug', 'oib', 'street', 'street_no', 'city', 'state', 'email', 'phone', 'is_published', 'is_link_active', 'referrals_count', 'published_at'
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
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('icon')->singleFile();
        $this->addMediaCollection('banner')->singleFile();
    }
}