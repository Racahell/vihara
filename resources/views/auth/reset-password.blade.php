@extends('layouts.auth')
@section('title', 'Reset Password')
@section('content')
<h2>Reset Password</h2>
<p class="muted">Masukkan password baru untuk akun Anda.</p>
<form action="{{ route('password.update') }}" method="POST" class="form-grid">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
    </div>
    <div>
        <label for="password">Password Baru</label>
        <input id="password" type="password" name="password" required>
    </div>
    <div>
        <label for="password_confirmation">Konfirmasi Password Baru</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
    </div>
    <button class="btn btn-primary" type="submit">Reset Password</button>
</form>

<div class="card" style="margin-top:14px;">
    <h3 style="margin-bottom:6px;">Belum Terima Email Reset?</h3>
    <p class="muted" style="margin:0 0 8px;">Masukkan email akun untuk kirim ulang link reset password.</p>
    <form method="POST" action="{{ route('forgot-password.email') }}" class="form-grid">
        @csrf
        <input type="email" name="email" placeholder="Masukkan email akun" value="{{ old('email', $email) }}" required>
        <button class="btn btn-secondary" type="submit">Kirim Ulang Email Reset</button>
    </form>
</div>

<div class="auth-links">
    <p class="muted"><a href="{{ route('login') }}">&larr; Kembali ke Login</a></p>
</div>
@endsection
