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
        'participant_age',
        'participant_gender',
        'participant_address',
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
        'participant_age' => 'integer',
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
