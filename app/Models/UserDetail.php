<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{

    protected $fillable = [
        'user_id', 'fname', 'lname', 'address', 'zip', 'city', 'state', 'phone',
        'avatar', 'bio', 'social', 'role', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public const ROLES = ['master', 'admin', 'editor', 'company_owner'];


    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
