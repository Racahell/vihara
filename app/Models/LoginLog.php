<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    public const CREATED_AT = 'logged_in_at';
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'logged_in_at',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'logged_in_at' => 'datetime',
    ];
}
