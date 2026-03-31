<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName ?? config('app.name') }} - @yield('title', 'Autentikasi') </title>
    @if(!empty($websiteSettings['website_favicon_path']))
        <link rel="icon" href="{{ asset('storage/' . $websiteSettings['website_favicon_path']) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-page">
    <div class="auth-shell">
        <div class="auth-card">
            <div class="auth-logo-wrap">
                @if(!empty($websiteSettings['website_logo_path']))
                    <img class="auth-logo-image" src="{{ asset('storage/' . $websiteSettings['website_logo_path']) }}" alt="Logo {{ $siteName ?? config('app.name') }}">
                @else
                    <div class="auth-logo-mark" aria-hidden="true">
                        <svg viewBox="0 0 64 64" role="img">
                            <circle cx="32" cy="32" r="30" fill="#ecfdf5" stroke="#99f6e4" stroke-width="2"/>
                            <path d="M13 39c6 0 10-3 19-3s13 3 19 3" fill="none" stroke="#0f766e" stroke-width="3" stroke-linecap="round"/>
                            <path d="M24 30l8-12 8 12" fill="none" stroke="#ec4899" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M29 30h6" fill="none" stroke="#ec4899" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <strong class="auth-logo-title">{{ $siteName ?? config('app.name') }}</strong>
                </div>
            </div>

            <div class="auth-top-nav">
                <a href="{{ route('guest.home') }}">Masuk sebagai Guest</a>
            </div>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </div>
    </div>

    @include('layouts.footer')
</div>
</body>
</html>
