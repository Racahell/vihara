<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class PengurusController extends Controller
{
    public function index()
    {
        $pengurus = User::with('roles')
            ->whereHas('roles', function ($query): void {
                $query->whereIn('slug', ['admin', 'owner', 'petugas']);
            })
            ->whereDoesntHave('roles', function ($query): void {
                $query->where('slug', 'superadmin');
            })
            ->latest()
            ->paginate(15);

        return view('admin.pengurus', [
            'pengurus' => $pengurus,
        ]);
    }
}
