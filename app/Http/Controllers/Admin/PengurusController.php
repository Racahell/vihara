<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;

class PengurusController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $pengurus = User::with('roles')
            ->whereHas('roles', function ($query): void {
                $query->whereIn('slug', ['admin', 'owner', 'petugas']);
            })
            ->whereDoesntHave('roles', function ($query): void {
                $query->where('slug', 'superadmin');
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.pengurus', [
            'pengurus' => $pengurus,
            'perPage' => $perPage,
        ]);
    }
}
