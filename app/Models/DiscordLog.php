<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscordLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['event', 'status_code', 'payload', 'response_body', 'created_at'];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];
}
