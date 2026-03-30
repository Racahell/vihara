<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    protected $table = 'pengguna';
    protected $primaryKey = 'id_pengguna';
    public $timestamps = false; // kalau tabel kamu gak pakai created_at & updated_at

    protected $fillable = [
        'nama',
        'username',
        'password',
        'peran'
    ];

    protected $hidden = [
        'password'
    ];

    // Pakai username sebagai auth identifier
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}