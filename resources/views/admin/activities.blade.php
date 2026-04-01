@extends('layouts.app')
@section('title', 'Kegiatan')
@section('content')
@php
    $isManager = auth()->user()->hasAnyRole(['superadmin', 'admin']);
    $tab = $tab ?? 'active';
@endphp

<div class="grid-2">
    @if($isManager)
        <div class="card">
            <h3>Buat Kegiatan</h3>
            <form action="{{ route('admin.activities.store') }}" method="POST" class="form-grid">
                @csrf
                <input type="text" name="title" placeholder="Judul kegiatan" required>
                <textarea name="description" placeholder="Deskripsi"></textarea>
                <input type="text" name="location" placeholder="Lokasi">
                <input type="datetime-local" name="start_at" required>
                <input type="datetime-local" name="end_at">
                <input type="number" name="quota" placeholder="Kuota" min="1" required>
                <button type="submit">Simpan Kegiatan</button>
            </form>
        </div>
    @endif
    <div class="card">
        <h3>Catatan</h3>
        <p class="muted">Hapus data kegiatan oleh admin/superadmin memakai soft delete. Data masuk ke tab Deleted dan bisa direstore oleh superadmin.</p>
    </div>
</div>

<div class="card" style="margin-top:14px;">
    <div class="table-toolbar" style="margin:0;">
        <div class="tabs">
            <a href="{{ route('admin.activities.index', ['tab' => 'active', 'per_page' => $perPage ?? 10]) }}" class="tab {{ $tab === 'active' ? 'active' : '' }}">Kegiatan Aktif</a>
            @if($canViewDeleted ?? false)
                <a href="{{ route('admin.activities.index', ['tab' => 'deleted', 'per_page' => $perPage ?? 10]) }}" class="tab {{ $tab === 'deleted' ? 'active' : '' }}">Deleted</a>
            @endif
        </div>
        <form method="GET" class="table-length">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <label for="activities-per-page">Tampilkan</label>
            <select id="activities-per-page" name="per_page" onchange="this.form.submit()">
                @foreach([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" @selected((int) ($perPage ?? 10) === $size)>{{ $size }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@if($tab === 'deleted' && ($canViewDeleted ?? false))
    <div class="table-wrap" style="margin-top:14px;">
        <table>
            <thead>
            <tr>
                <th>Judul</th>
                <th>Waktu</th>
                <th>Dihapus Pada</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($deletedActivities as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->start_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>{{ $activity->deleted_at?->format('d-m-Y H:i:s') ?? '-' }}</td>
                    <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <form action="{{ route('admin.activities.restore', $activity->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary">Restore</button>
                            </form>
                            <form action="{{ route('admin.activities.force-delete', $activity->id) }}" method="POST" onsubmit="return confirm('Yakin hapus permanen kegiatan ini? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete Permanent</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada kegiatan yang dihapus.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $deletedActivities?->appends(['tab' => 'deleted'])->links() }}
@else
    <div class="table-wrap" style="margin-top:14px;">
        <table>
            <thead>
            <tr>
                <th>Judul</th>
                <th>Waktu</th>
                <th>Kuota</th>
                <th>Terdaftar</th>
                <th>Status</th>
                @if($isManager)
                    <th>Aksi</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @forelse($activities as $activity)
                <tr>
                    <td>{{ $activity->title }}</td>
                    <td>{{ $activity->start_at?->format('d-m-Y H:i') ?? '-' }}</td>
                    <td>{{ $activity->quota }}</td>
                    <td>{{ $activity->registered_count }}</td>
                    <td>{{ $activity->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                    @if($isManager)
                        <td>
                            <button type="button" class="btn btn-outline" data-modal-open="edit-activity-modal-{{ $activity->id }}">Detail</button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ $isManager ? 6 : 5 }}">Belum ada data kegiatan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $activities->appends(['tab' => 'active'])->links() }}
@endif

@if($isManager)
    @foreach($activities as $activity)
        <div class="modal" id="edit-activity-modal-{{ $activity->id }}" aria-hidden="true">
            <div class="modal-backdrop" data-modal-close="edit-activity-modal-{{ $activity->id }}"></div>
            <div class="modal-dialog">
                <div class="modal-header">
                    <div>
                        <h3>Detail Kegiatan</h3>
                        <div class="muted">Ubah data atau hapus kegiatan</div>
                    </div>
                    <button type="button" class="btn btn-secondary" data-modal-close="edit-activity-modal-{{ $activity->id }}">Batal</button>
                </div>
                <div class="modal-body">
                    <form id="delete-activity-form-{{ $activity->id }}" action="{{ route('admin.activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('Yakin hapus kegiatan ini? Data akan masuk ke tab Deleted.');">
                        @csrf
                        @method('DELETE')
                    </form>
                    <form id="edit-activity-form-{{ $activity->id }}" action="{{ route('admin.activities.update', $activity) }}" method="POST" class="form-grid">
                        @csrf
                        @method('PUT')
                        <input type="text" name="title" value="{{ $activity->title }}" placeholder="Judul kegiatan" required>
                        <textarea name="description" placeholder="Deskripsi">{{ $activity->description }}</textarea>
                        <input type="text" name="location" value="{{ $activity->location }}" placeholder="Lokasi">
                        <input type="datetime-local" name="start_at" value="{{ $activity->start_at?->format('Y-m-d\\TH:i') }}" required>
                        <input type="datetime-local" name="end_at" value="{{ $activity->end_at?->format('Y-m-d\\TH:i') }}">
                        <input type="number" name="quota" value="{{ $activity->quota }}" placeholder="Kuota" min="1" required>
                        <select name="is_active" required>
                            <option value="1" @selected($activity->is_active)>Aktif</option>
                            <option value="0" @selected(! $activity->is_active)>Nonaktif</option>
                        </select>
                        <div class="modal-footer-actions">
                            <button class="btn btn-secondary" type="button" data-modal-close="edit-activity-modal-{{ $activity->id }}">Batal</button>
                            <button class="btn btn-primary" type="submit" form="edit-activity-form-{{ $activity->id }}">Edit</button>
                            <button class="btn btn-danger" type="submit" form="delete-activity-form-{{ $activity->id }}">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
