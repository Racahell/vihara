<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'activity_registration_id',
        'activity_id',
        'user_id',
        'checked_in_at',
        'method',
        'handled_by',
        'notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(ActivityRegistration::class, 'activity_registration_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
