@extends('layouts.auth')
@section('title', 'Daftar')
@section('content')
<h2>Daftar Akun Umat</h2>
<p class="muted">Akun akan aktif setelah verifikasi email.</p>
<form action="{{ route('register.store') }}" method="POST" class="form-grid">
    @csrf
    <input type="hidden" name="captcha_mode" id="captcha_mode" value="online">
    <div>
        <label for="name">Nama lengkap</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required>
    </div>
    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div>
        <label for="phone">Nomor HP</label>
        <input id="phone" type="text" name="phone" value="{{ old('phone') }}">
    </div>
    <div>
        <label for="gender">Jenis Kelamin</label>
        <select id="gender" name="gender" required>
            <option value="">Pilih jenis kelamin</option>
            <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
            <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
        </select>
    </div>
    <div>
        <label for="address">Alamat</label>
        <input id="address" type="text" name="address" value="{{ old('address') }}" required>
    </div>
    <div>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>
    </div>
    <div>
        <label for="password_confirmation">Konfirmasi Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>
    </div>

    @if($useRecaptcha)
        <div class="captcha-block" id="recaptcha-block">
            <label>Google reCAPTCHA</label>
            <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
        </div>
    @endif

    @if($useOfflineCaptcha)
        <div class="captcha-block" id="offline-captcha-block">
            <label for="offline_captcha_answer">Captcha lokal</label>
            <div class="captcha-challenge">Jawab: {{ $offlineCaptchaQuestion }}</div>
            <input id="offline_captcha_answer" type="text" name="offline_captcha_answer" value="{{ old('offline_captcha_answer') }}" placeholder="Isi hasil perhitungan">
            <small class="muted">Captcha lokal digunakan saat perangkat offline.</small>
        </div>
    @endif

    <button class="btn btn-primary" type="submit">Daftar</button>
</form>
<div class="auth-links">
    <p class="muted">Sudah punya akun? <a href="{{ route('login') }}"><strong>Login</strong></a></p>
    <p class="muted"><a href="{{ route('guest.home') }}">&larr; Kembali ke Beranda</a></p>
</div>

@if($useRecaptcha)
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
<script>
    (() => {
        const modeInput = document.getElementById('captcha_mode');
        const recaptchaBlock = document.getElementById('recaptcha-block');
        const offlineBlock = document.getElementById('offline-captcha-block');
        const hasRecaptcha = {{ $useRecaptcha ? 'true' : 'false' }};
        const hasOffline = {{ $useOfflineCaptcha ? 'true' : 'false' }};

        const syncCaptchaMode = () => {
            const isOnline = navigator.onLine;
            let mode = 'online';

            if (hasRecaptcha && isOnline) {
                mode = 'online';
                if (recaptchaBlock) recaptchaBlock.style.display = '';
                if (offlineBlock) offlineBlock.style.display = 'none';
            } else if (hasOffline) {
                mode = 'offline';
                if (recaptchaBlock) recaptchaBlock.style.display = 'none';
                if (offlineBlock) offlineBlock.style.display = '';
            } else if (hasRecaptcha) {
                mode = 'online';
                if (recaptchaBlock) recaptchaBlock.style.display = '';
            }

            if (modeInput) modeInput.value = mode;
        };

        syncCaptchaMode();
        window.addEventListener('online', syncCaptchaMode);
        window.addEventListener('offline', syncCaptchaMode);
    })();
</script>
@endsection
