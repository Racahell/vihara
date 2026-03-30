@extends('layouts.app')
@section('title', 'Kegiatan')
@section('content')
<div class="grid-2">
    @if(auth()->user()->hasAnyRole(['superadmin', 'admin']))
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
        <p class="muted">Owner/Ketua menggunakan halaman ini sebagai monitoring (view-only) sesuai role.</p>
    </div>
</div>

<div class="table-wrap" style="margin-top:14px;">
<table>
    <thead><tr><th>Judul</th><th>Waktu</th><th>Kuota</th><th>Terdaftar</th><th>Status</th></tr></thead>
    <tbody>
    @foreach($activities as $activity)
        <tr>
            <td>{{ $activity->title }}</td>
            <td>{{ $activity->start_at->format('d-m-Y H:i') }}</td>
            <td>{{ $activity->quota }}</td>
            <td>{{ $activity->registered_count }}</td>
            <td>{{ $activity->is_active ? 'Aktif' : 'Nonaktif' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
{{ $activities->links() }}
@endsection
