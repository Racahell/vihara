@extends('layouts.app')
@section('title', 'Log Sistem')
@section('content')
<div class="card page-head">
    <h2>Log Sistem</h2>
    <p class="muted">Pantau log aktivitas, login, Discord, dan error aplikasi dalam satu halaman.</p>
</div>

<div class="card" style="margin-top:16px;">
    <div class="tabs">
        <a href="{{ route('admin.logs.index', ['tab' => 'activity']) }}" class="tab {{ $tab === 'activity' ? 'active' : '' }}">Log Aktivitas</a>
        <a href="{{ route('admin.logs.index', ['tab' => 'login']) }}" class="tab {{ $tab === 'login' ? 'active' : '' }}">Log Login</a>
        @if(in_array('discord', $allowedTabs ?? [], true))
            <a href="{{ route('admin.logs.index', ['tab' => 'discord']) }}" class="tab {{ $tab === 'discord' ? 'active' : '' }}">Log Discord</a>
        @endif
        @if(in_array('error', $allowedTabs ?? [], true))
            <a href="{{ route('admin.logs.index', ['tab' => 'error']) }}" class="tab {{ $tab === 'error' ? 'active' : '' }}">Log Error</a>
        @endif
    </div>
</div>

@if($tab === 'activity')
    <p class="muted" style="margin-top:12px;">
        Menampilkan {{ $activityLogs?->firstItem() ?? 0 }}-{{ $activityLogs?->lastItem() ?? 0 }} dari total {{ $activityLogs?->total() ?? 0 }} data.
    </p>
    <div class="table-wrap" style="margin-top:12px;">
        <table>
            <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Deskripsi</th><th>Target</th></tr></thead>
            <tbody>
            @forelse($activityLogs as $log)
                <tr>
                    <td>{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $log->user_id ?? '-' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->target_type }}#{{ $log->target_id }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada log aktivitas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $activityLogs?->links() }}
@endif

@if($tab === 'login')
    <p class="muted" style="margin-top:12px;">
        Menampilkan {{ $loginLogs?->firstItem() ?? 0 }}-{{ $loginLogs?->lastItem() ?? 0 }} dari total {{ $loginLogs?->total() ?? 0 }} data.
    </p>
    <div class="table-wrap" style="margin-top:12px;">
        <table>
            <thead><tr><th>Waktu</th><th>Email</th><th>IP</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($loginLogs as $log)
                <tr>
                    <td>{{ $log->logged_in_at?->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $log->email }}</td>
                    <td>{{ $log->ip_address }}</td>
                    <td>{{ $log->successful ? 'Berhasil' : 'Gagal' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada log login.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $loginLogs?->links() }}
@endif

@if($tab === 'discord')
    <p class="muted" style="margin-top:12px;">
        Menampilkan {{ $discordLogs?->firstItem() ?? 0 }}-{{ $discordLogs?->lastItem() ?? 0 }} dari total {{ $discordLogs?->total() ?? 0 }} data.
    </p>
    <div class="table-wrap" style="margin-top:12px;">
        <table>
            <thead><tr><th>Waktu</th><th>Event</th><th>Status Code</th><th>Response</th></tr></thead>
            <tbody>
            @forelse($discordLogs as $log)
                <tr>
                    <td>{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $log->event }}</td>
                    <td>{{ $log->status_code }}</td>
                    <td>{{ \Illuminate\Support\Str::limit((string) $log->response_body, 120) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada log Discord.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $discordLogs?->links() }}
@endif

@if($tab === 'error')
    <p class="muted" style="margin-top:12px;">
        Menampilkan {{ $errorLogs?->firstItem() ?? 0 }}-{{ $errorLogs?->lastItem() ?? 0 }} dari total {{ $errorLogs?->total() ?? 0 }} baris log.
    </p>
    <div class="table-wrap" style="margin-top:12px;">
        <table>
            <thead><tr><th>Baris Log Error (Terbaru)</th></tr></thead>
            <tbody>
            @forelse($errorLogs as $line)
                <tr><td><code>{{ $line }}</code></td></tr>
            @empty
                <tr><td>File log error belum tersedia.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $errorLogs?->links() }}
@endif
@endsection
