<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $siteName ?? config('app.name') }} - @yield('title', 'Dashboard') </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <h3>Vihara</h3>
            <div class="muted">Sistem Kegiatan, Donasi, dan Absensi</div>
            <div class="pill" style="margin-top:8px;">Role: {{ strtoupper($currentRoleSlug ?? '-') }}</div>
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

        <div class="boat-lotus">
            <div>Perahu melambangkan perjalanan kebajikan.</div>
            <div>Teratai melambangkan kemurnian batin.</div>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div>
                <strong>{{ auth()->user()->name ?? '-' }}</strong>
                <div class="muted">{{ auth()->user()->email ?? '-' }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-secondary" type="submit">Logout</button>
            </form>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        @yield('content')

        @include('layouts.footer')
    </main>
</div>
</body>
</html>
