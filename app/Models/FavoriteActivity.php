<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteActivity extends Model
{
    protected $fillable = ['activity_id', 'user_id'];
}
