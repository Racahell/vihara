<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName ?? config('app.name') }} - @yield('title', 'Dashboard') </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if(!empty($websiteSettings['website_favicon_path']))
        <link rel="icon" href="{{ asset('storage/' . $websiteSettings['website_favicon_path']) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@php
    $pageTitle = trim((string) $__env->yieldContent('title', 'Dashboard'));
@endphp
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-head">
                @if(!empty($websiteSettings['website_logo_path']))
                    <img src="{{ asset('storage/' . $websiteSettings['website_logo_path']) }}" alt="Logo {{ $siteName ?? config('app.name') }}" class="brand-logo" width="56" height="56">
                @else
                    <span class="brand-logo-fallback" aria-hidden="true">
                        <svg viewBox="0 0 64 64" role="img">
                            <rect x="2" y="2" width="60" height="60" rx="18" fill="#5E7370"/>
                            <path d="M13 41c7-8 14-12 23-12s17 4 25 12H13z" fill="#D4DEDC"/>
                            <path d="M32 14c3 4 5 8 5 12a5 5 0 1 1-10 0c0-4 2-8 5-12z" fill="#ffffff"/>
                        </svg>
                    </span>
                @endif
                <h3>{{ $siteName ?? config('app.name') }}</h3>
            </div>
        </div>

        @foreach($sidebarMenuGroups ?? [] as $group)
            <div class="menu-group">
                <div class="menu-group-title">{{ $group['title'] }}</div>
                <ul class="menu-list">
                    @foreach($group['items'] as $item)
                        <li>
                            <a href="{{ $item['href'] }}" class="{{ $item['is_active'] ? 'active' : '' }}">
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

    </aside>

    <main class="main">
        <div class="topbar">
            <h1 class="topbar-title">{{ $pageTitle }}</h1>
            <div class="topbar-actions">
                <div class="topbar-user">
                    @if(!empty(auth()->user()?->profile_photo_path))
                        <img class="topbar-avatar" src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Foto profil">
                    @else
                        <div class="topbar-avatar-fallback">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                    @endif
                    <div>
                        <strong>{{ auth()->user()->name ?? '-' }}</strong>
                        <div class="muted">{{ auth()->user()->email ?? '-' }}</div>
                    </div>
                </div>
                <a class="btn btn-secondary" href="{{ route('profile.show') }}">Profil Saya</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-secondary" type="submit">Logout</button>
                </form>
            </div>
        </div>

        <div class="main-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            @yield('content')

            @include('layouts.footer')
        </div>
    </main>
</div>
</body>
</html>


