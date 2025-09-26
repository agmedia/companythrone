<?php

namespace App\Models\Back\Banners;

use Illuminate\Database\Eloquent\Model;

class BannerSchedule extends Model
{
    protected $table = 'banner_schedules';
    protected $guarded = ['id'];
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'position'   => 'integer',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}
