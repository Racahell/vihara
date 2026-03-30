@extends('layouts.auth')
@section('title', 'Login')
@section('content')
<h2>Login</h2>
<p class="muted">Silakan masuk untuk melanjutkan aktivitas akun Anda.</p>
<form action="{{ route('login.attempt') }}" method="POST" class="form-grid">
    @csrf
    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
    <input type="password" name="password" placeholder="Password" required>
    <label class="remember-row"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
    <button class="btn btn-primary" type="submit">Login</button>
</form>
<div class="auth-links">
    <p class="muted">Belum punya akun? <a href="{{ route('register') }}"><strong>Daftar sekarang</strong></a></p>
    <p class="muted"><a href="{{ route('guest.home') }}">&larr; Kembali ke Beranda</a></p>
</div>
@endsection
