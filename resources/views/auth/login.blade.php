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

@if($showForgotPassword ?? false)
<div class="card" style="margin-top:14px;">
    <h3 style="margin-bottom:6px;">Lupa Password</h3>
    <p class="muted" style="margin:0 0 8px;">Kirim atau kirim ulang link reset password lewat email atau WhatsApp.</p>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button class="btn btn-primary" type="button" data-forgot-method="email">Email</button>
        <button class="btn btn-secondary" type="button" data-forgot-method="whatsapp">WhatsApp</button>
    </div>

    <form method="POST" action="{{ route('forgot-password.email') }}" class="form-grid" data-forgot-form="email" style="display:none;">
        @csrf
        <input type="email" name="email" placeholder="Masukkan email akun" value="{{ old('email') }}" required>
        <button class="btn btn-primary" type="submit">Kirim / Kirim Ulang Link Email</button>
    </form>

    <form method="POST" action="{{ route('forgot-password.whatsapp') }}" class="form-grid" data-forgot-form="whatsapp" style="display:none;">
        @csrf
        <input type="text" name="phone" placeholder="Masukkan No. Tlp / WhatsApp" value="{{ old('phone') }}" required>
        <button class="btn btn-secondary" type="submit">Buat / Kirim Ulang Link WhatsApp</button>
    </form>
    @if(!empty($waResetUrl))
        <a class="btn btn-green" href="{{ $waResetUrl }}" target="_blank" rel="noopener" style="margin-top:10px;">Kirim via WhatsApp</a>
    @endif
    @error('reset_password')
        <div class="alert alert-error" style="margin-top:10px;">{{ $message }}</div>
    @enderror
</div>
@endif

@if(($showResendVerification ?? false) || $errors->has('resend_verification'))
<div class="card" style="margin-top:14px;">
    <h3 style="margin-bottom:6px;">Belum Terima Email Verifikasi?</h3>
    <p class="muted" style="margin:0 0 8px;">Masukkan email akun untuk kirim ulang link aktivasi.</p>
    <form method="POST" action="{{ route('verify.email.resend') }}" class="form-grid">
        @csrf
        <input type="email" name="email" placeholder="Masukkan email akun" value="{{ old('email', $pendingVerificationEmail ?? '') }}" required>
        <button class="btn btn-secondary" type="submit">Kirim Ulang Email Verifikasi</button>
    </form>
    @error('resend_verification')
        <div class="alert alert-error" style="margin-top:10px;">{{ $message }}</div>
    @enderror
</div>
@endif
<script>
(() => {
    const buttons = document.querySelectorAll('[data-forgot-method]');
    const forms = document.querySelectorAll('[data-forgot-form]');
    if (!buttons.length || !forms.length) return;
    const preferredMethod = @json($forgotPasswordMethod ?? '');

    const showForm = (method) => {
        forms.forEach((form) => {
            form.style.display = form.dataset.forgotForm === method ? 'grid' : 'none';
        });
    };

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => showForm(btn.dataset.forgotMethod));
    });

    if (preferredMethod === 'email' || preferredMethod === 'whatsapp') {
        showForm(preferredMethod);
    }
})();
</script>
<div class="auth-links">
    <p class="muted">Belum punya akun? <a href="{{ route('register') }}"><strong>Daftar sekarang</strong></a></p>
    <p class="muted"><a href="{{ route('guest.home') }}">&larr; Kembali ke Beranda</a></p>
</div>
@endsection
