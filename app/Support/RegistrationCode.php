<?php

namespace App\Support;

use Illuminate\Support\Str;

class RegistrationCode
{
    public static function make(string $prefix): string
    {
        return strtoupper($prefix) . '-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
    }
}
