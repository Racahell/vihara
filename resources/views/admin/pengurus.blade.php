@extends('layouts.app')
@section('title', 'Pengurus')
@section('content')
<div class="card page-head">
    <h2>Pengurus</h2>
    <p class="muted">Daftar pengurus vihara (view-only).</p>
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

