<?php

namespace App\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class DailySession extends Model
{

    protected $fillable = ['company_id', 'day', 'completed_25', 'completed_count', 'slots_payload', 'last_action_at'];

    protected $casts = ['completed_25' => 'bool', 'day' => 'date', 'slots_payload' => 'array'];


    public function company()
    {
        return $this->belongsTo(\App\Models\Back\Catalog\Company::class);
    }
}
