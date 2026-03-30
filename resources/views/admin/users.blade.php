@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="card page-head">
    <h2>Manajemen Pengguna</h2>
    <p class="muted">Kelola akun, role, dan status pengguna.</p>
</div>

<div class="card" style="margin-top:16px;">
    <form method="GET" class="table-toolbar" style="margin:0;">
        <div class="table-length">
            <label for="perPage">Tampilkan</label>
            <select id="perPage" name="per_page" onchange="this.form.submit()">
                @foreach([10,25,50,100] as $size)
                    <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                @endforeach
            </select>
            <span>data</span>
        </div>

        <div class="table-search">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, email, username...">
            <button class="btn btn-secondary" type="submit">Cari</button>
        </div>
    </form>
</div>

<div class="table-wrap" style="margin-top:12px;">
<table class="users-table">
    <thead>
    <tr>
        <th style="width:22%;">Nama</th>
        <th style="width:30%;">Email</th>
        <th style="width:16%;">Role</th>
        <th style="width:14%;">Status</th>
        <th style="width:18%;">Aksi</th>
    </tr>
    </thead>
    <tbody>
    @forelse($users as $user)
        @php
            $roleSlug = $user->roles->pluck('slug')->first() ?? 'umat';
            $roleLabel = strtoupper($roleSlug);
        @endphp
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><span class="badge role-{{ $roleSlug }}">{{ $roleLabel }}</span></td>
            <td>
                @if($user->is_active)
                    <span class="badge status-active">Aktif</span>
                @else
                    <span class="badge status-inactive">Nonaktif</span>
                @endif
            </td>
            <td>
                <button type="button" class="btn btn-outline" data-modal-open="user-modal-{{ $user->id }}">Detail</button>
            </td>
        </tr>
    @empty
        <tr><td colspan="5">Data pengguna tidak ditemukan.</td></tr>
    @endforelse
    </tbody>
</table>
</div>

{{ $users->links() }}

@foreach($users as $user)
    @php
        $roleId = $user->roles->pluck('id')->first();
    @endphp
    <div class="modal" id="user-modal-{{ $user->id }}" aria-hidden="true">
        <div class="modal-backdrop" data-modal-close="user-modal-{{ $user->id }}"></div>
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <h3>Detail Pengguna</h3>
                    <div class="muted">Kelola data dan akses pengguna</div>
                </div>
                <button type="button" class="btn btn-secondary" data-modal-close="user-modal-{{ $user->id }}">Tutup</button>
            </div>

            <div class="modal-body">
                <div class="detail-section">
                    <h4>Informasi Pengguna</h4>
                    <div class="detail-grid">
                        <div><strong>Nama</strong><div>{{ $user->name }}</div></div>
                        <div><strong>Email</strong><div>{{ $user->email }}</div></div>
                        <div><strong>IP Registrasi</strong><div>{{ $user->registration_ip ?? '-' }}</div></div>
                        <div><strong>Role</strong><div><span class="badge role-{{ $user->roles->pluck('slug')->first() ?? 'umat' }}">{{ strtoupper($user->roles->pluck('slug')->first() ?? 'UMAT') }}</span></div></div>
                        <div><strong>Status</strong><div>@if($user->is_active)<span class="badge status-active">Aktif</span>@else<span class="badge status-inactive">Nonaktif</span>@endif</div></div>
                        <div><strong>Tanggal dibuat</strong><div>{{ $user->created_at?->format('d-m-Y H:i') ?? '-' }}</div></div>
                        <div><strong>Terakhir login</strong><div>{{ $user->last_login_at?->format('d-m-Y H:i') ?? '-' }}</div></div>
                    </div>
                </div>

                @if(! $isOwnerReadOnly)
                    <div class="detail-section">
                        <h4>Edit Data</h4>
                        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="form-grid modal-edit-form">
                            @csrf
                            <div>
                                <label for="name-{{ $user->id }}">Nama</label>
                                <input id="name-{{ $user->id }}" type="text" name="name" value="{{ $user->name }}" required>
                            </div>
                            <div>
                                <label for="email-{{ $user->id }}">Email</label>
                                <input id="email-{{ $user->id }}" type="email" name="email" value="{{ $user->email }}" required>
                            </div>
                            <div>
                                <label for="phone-{{ $user->id }}">No HP</label>
                                <input id="phone-{{ $user->id }}" type="text" name="phone" value="{{ $user->phone }}" placeholder="No HP">
                            </div>
                            <div>
                                <label for="role-{{ $user->id }}">Role</label>
                                <select id="role-{{ $user->id }}" name="role_id" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @selected($roleId === $role->id)>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status-{{ $user->id }}">Status</label>
                                <select id="status-{{ $user->id }}" name="is_active" required>
                                    <option value="1" @selected($user->is_active)>Aktif</option>
                                    <option value="0" @selected(! $user->is_active)>Nonaktif</option>
                                </select>
                            </div>

                            <div class="modal-footer-actions">
                                <button class="btn btn-secondary" type="button" data-modal-close="user-modal-{{ $user->id }}">Tutup</button>
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </form>
                    </div>

                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" type="submit">Hapus User</button>
                    </form>
                @else
                    <div class="detail-section">
                        <p class="muted" style="margin:0;">Akun Owner/Ketua bersifat view-only. Edit, ubah role, dan hapus pengguna dinonaktifkan.</p>
                    </div>
                    <div class="modal-footer-actions">
                        <button class="btn btn-secondary" type="button" data-modal-close="user-modal-{{ $user->id }}">Tutup</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach
@endsection
