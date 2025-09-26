<?php

namespace App\Models\Back\Banners;

use Illuminate\Database\Eloquent\Model;

class BannerTranslation extends Model
{
    protected $table = 'banner_translations';
    protected $guarded = ['id'];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}
