<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationVerificationLog extends Model
{
    protected $fillable = ['donation_id', 'acted_by', 'action', 'reason'];
}
