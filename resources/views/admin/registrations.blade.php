@extends('layouts.app')
@section('title', 'Pendaftaran Kegiatan')
@section('content')
<div class="card">
    <h3>Filter Pendaftaran Kegiatan</h3>
    <form method="GET" class="form-grid" style="grid-template-columns:repeat(6,minmax(0,1fr));align-items:end;">
        <div>
            <div class="muted">Dari Tanggal</div>
            <input type="date" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div>
            <div class="muted">Sampai Tanggal</div>
            <input type="date" name="end_date" value="{{ request('end_date') }}">
        </div>
        <div>
            <div class="muted">Kegiatan</div>
            <select name="activity_id">
                <option value="">Semua kegiatan</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}" @selected((string) request('activity_id') === (string) $activity->id)>{{ $activity->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <div class="muted">Tampilkan</div>
            <select name="per_page">
                @foreach([10, 20, 50, 100] as $size)
                    <option value="{{ $size }}" @selected(($perPage ?? 20) === $size)>{{ $size }} data</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-green" type="submit">Terapkan Filter</button>
    </form>
    <div class="cards" style="margin-top:12px;">
        <div class="card"><div class="muted">Total Pendaftaran</div><h3>{{ number_format($summary['total']) }}</h3></div>
        <div class="card"><div class="muted">Sudah Hadir</div><h3>{{ number_format($summary['hadir']) }}</h3></div>
        <div class="card"><div class="muted">Belum Hadir</div><h3>{{ number_format($summary['belum']) }}</h3></div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a class="btn" href="{{ route('admin.registrations.excel', request()->query()) }}">Export Excel</a>
        <a class="btn btn-green" href="{{ route('admin.registrations.pdf', request()->query()) }}">Export PDF</a>
        <a class="btn btn-secondary" href="{{ route('admin.registrations.print', request()->query()) }}" target="_blank">Print</a>
    </div>
</div>

<div class="card" style="margin-top:12px;">
    <p class="table-range" style="margin:0;">
        Menampilkan {{ $registrations->firstItem() ?? 0 }}-{{ $registrations->lastItem() ?? 0 }} dari {{ $registrations->total() }} data
    </p>
</div>

<div class="table-wrap">
<table>
    <thead><tr><th>Kode</th><th>Peserta</th><th>Usia</th><th>JK</th><th>Alamat</th><th>Kegiatan</th><th>Tipe</th><th>Status Hadir</th><th>Waktu Daftar</th></tr></thead>
    <tbody>
    @forelse($registrations as $reg)
        <tr>
            <td>{{ $reg->registration_code }}</td>
            <td>{{ $reg->participant_name }}</td>
            <td>{{ $reg->participant_age ?? '-' }}</td>
            <td>{{ $reg->participant_gender ?? '-' }}</td>
            <td>{{ $reg->participant_address ?? '-' }}</td>
            <td>{{ $reg->activity->title ?? '-' }}</td>
            <td>{{ strtoupper($reg->registration_type) }}</td>
            <td>{{ strtoupper($reg->attendance_status) }}</td>
            <td>{{ $reg->registered_at?->format('d-m-Y H:i') }}</td>
        </tr>
    @empty
        <tr><td colspan="8">Tidak ada data pendaftaran pada filter ini.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
{{ $registrations->links() }}
@endsection
