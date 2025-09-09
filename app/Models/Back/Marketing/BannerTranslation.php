<?php

namespace App\Models\Back\Marketing;

use Illuminate\Database\Eloquent\Model;

class BannerTranslation extends Model
{

    protected $fillable = ['banner_id','locale','title','slogan','url'];
}
