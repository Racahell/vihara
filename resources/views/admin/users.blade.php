@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')
@php
    $tab = $tab ?? 'active';
@endphp
<div class="card page-head">
    <h2>Manajemen Pengguna</h2>
    <p class="muted">Kelola akun, role, dan status pengguna.</p>
</div>

<div class="card" style="margin-top:16px;">
    <div class="tabs">
        <a href="{{ route('admin.users.index', ['tab' => 'active']) }}" class="tab {{ $tab === 'active' ? 'active' : '' }}">User Aktif</a>
        @if($canViewDeleted ?? false)
            <a href="{{ route('admin.users.index', ['tab' => 'deleted']) }}" class="tab {{ $tab === 'deleted' ? 'active' : '' }}">Deleted</a>
        @endif
    </div>
</div>

<div class="card" style="margin-top:16px;">
    <form method="GET" class="table-toolbar" style="margin:0;">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <div class="table-length">
                <label for="perPage">Tampilkan</label>
                <select id="perPage" name="per_page" onchange="this.form.submit()">
                    @foreach([10,25,50,100] as $size)
                        <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <span>data</span>
            </div>
            @if(($canCreateUser ?? false) && $tab === 'active')
                <button type="button" class="btn btn-primary" data-modal-open="create-user-modal">Tambah User</button>
            @endif
        </div>

        <div class="table-search">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, email, username...">
            <button class="btn btn-secondary" type="submit">Cari</button>
        </div>
    </form>
</div>

@if($tab === 'deleted' && ($canViewDeleted ?? false))
    <div class="table-wrap" style="margin-top:12px;">
    <table class="users-table">
        <thead>
        <tr>
            <th style="width:22%;">Nama</th>
            <th style="width:30%;">Email</th>
            <th style="width:16%;">Role</th>
            <th style="width:14%;">Dihapus Pada</th>
            <th style="width:18%;">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @forelse($deletedUsers as $user)
            @php
                $roleSlug = $user->roles->pluck('slug')->first() ?? 'umat';
                $roleLabel = strtoupper($roleSlug);
            @endphp
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><span class="badge role-{{ $roleSlug }}">{{ $roleLabel }}</span></td>
                <td>{{ $user->deleted_at?->format('d-m-Y H:i:s') ?? '-' }}</td>
                <td>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-secondary" type="submit">Restore</button>
                        </form>
                        <form action="{{ route('admin.users.force-delete', $user->id) }}" method="POST" onsubmit="return confirm('Yakin hapus permanen user ini? Tindakan ini tidak bisa dibatalkan.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Delete Permanent</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada user yang dihapus.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    {{ $deletedUsers?->appends(['tab' => 'deleted'])->links() }}
@else
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
    {{ $users->appends(['tab' => 'active'])->links() }}
@endif

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
                        <form id="update-user-form-{{ $user->id }}" action="{{ route('admin.users.update', $user) }}" method="POST" class="form-grid modal-edit-form">
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
                        </form>
                        <form id="delete-user-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                            @csrf
                            @method('DELETE')
                        </form>
                        <div class="modal-footer-actions modal-footer-actions-split">
                            <button class="btn btn-danger" type="submit" form="delete-user-form-{{ $user->id }}">Hapus User</button>
                            <div class="modal-footer-right">
                                <button class="btn btn-secondary" type="button" data-modal-close="user-modal-{{ $user->id }}">Tutup</button>
                                <button class="btn btn-primary" type="submit" form="update-user-form-{{ $user->id }}">Update</button>
                            </div>
                        </div>
                    </div>
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

@if($canCreateUser ?? false)
    <div class="modal" id="create-user-modal" aria-hidden="true">
        <div class="modal-backdrop" data-modal-close="create-user-modal"></div>
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <h3>Tambah User Baru</h3>
                    <div class="muted">Pengguna dengan hak akses Data User - Create dapat menambahkan user baru.</div>
                </div>
                <button type="button" class="btn btn-secondary" data-modal-close="create-user-modal">Tutup</button>
            </div>

            <div class="modal-body">
                <form action="{{ route('admin.users.store') }}" method="POST" class="form-grid modal-edit-form">
                    @csrf
                    <div>
                        <label for="create-name">Nama</label>
                        <input id="create-name" type="text" name="name" required>
                    </div>
                    <div>
                        <label for="create-email">Email</label>
                        <input id="create-email" type="email" name="email" required>
                    </div>
                    <div>
                        <label for="create-username">Username (opsional)</label>
                        <input id="create-username" type="text" name="username" placeholder="Otomatis jika dikosongkan">
                    </div>
                    <div>
                        <label for="create-phone">No HP</label>
                        <input id="create-phone" type="text" name="phone" placeholder="No HP">
                    </div>
                    <div>
                        <label for="create-password">Password</label>
                        <input id="create-password" type="password" name="password" minlength="8" required>
                    </div>
                    <div>
                        <label for="create-password-confirmation">Konfirmasi Password</label>
                        <input id="create-password-confirmation" type="password" name="password_confirmation" minlength="8" required>
                    </div>
                    <div>
                        <label for="create-role">Role</label>
                        <select id="create-role" name="role_id" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="create-status">Status</label>
                        <select id="create-status" name="is_active" required>
                            <option value="1" selected>Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>

                    <div class="modal-footer-actions">
                        <button class="btn btn-secondary" type="button" data-modal-close="create-user-modal">Tutup</button>
                        <button class="btn btn-primary" type="submit">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
