@extends('layouts.app')
@section('title', 'Laporan Donasi')
@section('content')
<div class="card">
    <h3>Laporan Keuangan Donasi Vihara</h3>
    <form method="GET" class="form-grid" style="grid-template-columns:repeat(5,minmax(0,1fr));align-items:end;">
        <div>
            <div class="muted">Mulai</div>
            <input type="date" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div>
            <div class="muted">Sampai</div>
            <input type="date" name="end_date" value="{{ request('end_date') }}">
        </div>
        <div>
            <div class="muted">Kategori</div>
            <select name="category_id">
                <option value="">Semua kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string)request('category_id') === (string)$cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <div class="muted">Kegiatan</div>
            <select name="activity_id">
                <option value="">Semua kegiatan</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}" @selected((string)request('activity_id') === (string)$activity->id)>{{ $activity->title }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-green">Filter</button>
    </form>

    <div class="cards" style="margin-top:12px;">
        <div class="card"><div class="muted">Total Donasi Masuk</div><h3>Rp {{ number_format($summary['total_masuk'], 0, ',', '.') }}</h3></div>
        <div class="card"><div class="muted">Donasi Terverifikasi</div><h3>Rp {{ number_format($summary['total_terverifikasi'], 0, ',', '.') }}</h3></div>
        <div class="card"><div class="muted">Donasi Pending</div><h3>Rp {{ number_format($summary['total_pending'], 0, ',', '.') }}</h3></div>
        <div class="card"><div class="muted">Donasi Ditolak</div><h3>Rp {{ number_format($summary['total_ditolak'], 0, ',', '.') }}</h3></div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a class="btn btn-green" href="{{ route('reports.donations.pdf', request()->query()) }}">Export PDF Resmi</a>
        <a class="btn" href="{{ route('reports.donations.excel', request()->query()) }}">Export Excel</a>
        <a class="btn btn-secondary" href="{{ route('reports.donations.print', request()->query()) }}" target="_blank">Print</a>
    </div>
</div>

<div class="card" style="margin-top:14px;">
    <h3>Komposisi Donasi (Filter Aktif)</h3>
    <div class="chart-wrap">
        <canvas
            data-chart="category-breakdown"
            data-labels='@json($categoryLabels)'
            data-values='@json($categoryValues)'></canvas>
    </div>
</div>

<div class="table-wrap" style="margin-top:14px;">
<table>
    <thead><tr><th>Tanggal</th><th>Kode Donasi</th><th>Donatur</th><th>Kategori</th><th>Kegiatan</th><th>Metode</th><th>Nominal</th><th>Pembayaran</th><th>Verifikasi</th><th>Kwitansi</th></tr></thead>
    <tbody>
    @forelse($donations as $donation)
        <tr>
            <td>{{ $donation->donated_at?->format('d-m-Y') }}</td>
            <td>DON-{{ str_pad((string)$donation->id, 6, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $donation->donor_name }}</td>
            <td>{{ $donation->category->name ?? '-' }}</td>
            <td>{{ $donation->activity->title ?? '-' }}</td>
            <td>{{ strtoupper((string) data_get($donation->payment_payload, 'channel', $donation->payment_method)) }}</td>
            <td>Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
            <td>{{ strtoupper($donation->payment_status) }}</td>
            <td>{{ strtoupper($donation->verification_status) }}</td>
            <td>{{ $donation->receipt_number ?? '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="10">Tidak ada data donasi pada filter ini.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
@endsection
