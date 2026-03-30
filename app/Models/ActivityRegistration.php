<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityRegistration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'activity_id',
        'user_id',
        'participant_name',
        'participant_phone',
        'registration_code',
        'qr_payload',
        'registration_type',
        'attendance_status',
        'registered_at',
        'checked_in_at',
        'checkin_method',
        'created_by',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
