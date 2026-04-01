@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="card page-head">
    <h2>Pusat Notifikasi</h2>
    <p class="muted">Semua notifikasi operasional, verifikasi, login, dan webhook Discord ada di sini.</p>
</div>

<div class="cards">
    <div class="card"><div class="muted">Donasi Pending</div><h2>{{ $summary['donasi_pending'] }}</h2></div>
    <div class="card"><div class="muted">Login Gagal Hari Ini</div><h2>{{ $summary['login_gagal_hari_ini'] }}</h2></div>
    <div class="card"><div class="muted">Event Discord Hari Ini</div><h2>{{ $summary['event_discord_hari_ini'] }}</h2></div>
    <div class="card"><div class="muted">Aktivitas Hari Ini</div><h2>{{ $summary['aktivitas_hari_ini'] }}</h2></div>
</div>

<div class="table-toolbar" style="margin-top:12px;">
    <div class="muted">Atur jumlah data per tabel notifikasi</div>
    <form method="GET" class="table-length">
        <label for="notifications-per-page">Tampilkan</label>
        <select id="notifications-per-page" name="per_page" onchange="this.form.submit()">
            @foreach([10, 25, 50, 100] as $size)
                <option value="{{ $size }}" @selected((int) ($perPage ?? 10) === $size)>{{ $size }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="grid-2" style="margin-top:12px;">
    <div class="table-wrap no-scroll">
        <table>
            <thead><tr><th colspan="3">Notifikasi Sistem Terbaru</th></tr></thead>
            <thead><tr><th>Waktu</th><th>Aksi</th><th>Deskripsi</th></tr></thead>
            <tbody>
            @forelse($recentNotifications as $item)
                <tr>
                    <td>{{ $item->created_at?->format('d-m-Y H:i:s') }}</td>
                    <td>{{ strtoupper($item->action) }}</td>
                    <td>{{ $item->description }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Belum ada notifikasi sistem.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $recentNotifications->links() }}
    </div>

    <div class="table-wrap no-scroll">
        <table>
            <thead><tr><th colspan="4">Notifikasi Discord Terbaru</th></tr></thead>
            <thead><tr><th>Waktu</th><th>Event</th><th>Status</th><th>Response</th></tr></thead>
            <tbody>
            @forelse($discordNotifications as $item)
                <tr>
                    <td>{{ $item->created_at?->format('d-m-Y H:i:s') }}</td>
                    <td>{{ $item->event }}</td>
                    <td>{{ $item->status_code }}</td>
                    <td>{{ \Illuminate\Support\Str::limit((string) $item->response_body, 80) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada notifikasi Discord.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $discordNotifications->links() }}
    </div>
</div>
@endsection
