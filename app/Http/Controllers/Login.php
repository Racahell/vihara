<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class Login extends Controller
{
    // tampilkan form login
    public function showLoginForm()
    {
        return view('login'); // pastikan ada resources/views/login.blade.php
    }

    // proses login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = Pengguna::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // login manual
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('/dashboard'); // ganti sesuai route dashboard
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput();
    }

    // logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}