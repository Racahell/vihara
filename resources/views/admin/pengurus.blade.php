@extends('layouts.app')
@section('title', 'Pengurus')
@section('content')
<div class="card page-head">
    <h2>Pengurus</h2>
    <p class="muted">Daftar pengurus vihara (view-only).</p>
</div>

<div class="table-toolbar" style="margin-top:12px;">
    <div class="muted">Total data: {{ $pengurus->total() }}</div>
    <form method="GET" class="table-length">
        <label for="pengurus-per-page">Tampilkan</label>
        <select id="pengurus-per-page" name="per_page" onchange="this.form.submit()">
            @foreach([10, 25, 50, 100] as $size)
                <option value="{{ $size }}" @selected((int) ($perPage ?? 10) === $size)>{{ $size }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="table-wrap" style="margin-top:12px;">
    <table>
        <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Terakhir Login</th>
        </tr>
        </thead>
        <tbody>
        @forelse($pengurus as $user)
            @php($roleSlug = $user->roles->pluck('slug')->first() ?? 'umat')
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><span class="badge role-{{ $roleSlug }}">{{ strtoupper($roleSlug) }}</span></td>
                <td>
                    @if($user->is_active)
                        <span class="badge status-active">Aktif</span>
                    @else
                        <span class="badge status-inactive">Nonaktif</span>
                    @endif
                </td>
                <td>{{ $user->last_login_at?->format('d-m-Y H:i') ?? '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada data pengurus.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{ $pengurus->links() }}
@endsection
