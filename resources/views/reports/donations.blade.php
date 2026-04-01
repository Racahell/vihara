@extends('layouts.app')
@section('title', 'Laporan Donasi')
@section('content')
<div class="card">
    <h3>Laporan Penerimaan dan Penggunaan Dana Donasi</h3>
    <form method="GET" class="form-grid" style="grid-template-columns:repeat(4,minmax(0,1fr));align-items:end;">
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
        <div>
            <div class="muted">Saldo Awal</div>
            <input type="text" name="saldo_awal" value="{{ request('saldo_awal', $ledger['saldo_awal'] ?? 0) }}" placeholder="Contoh: 2500000">
        </div>
        <div>
            <div class="muted">Total Penyaluran</div>
            <input type="text" name="total_penyaluran" value="{{ request('total_penyaluran', $ledger['total_penyaluran'] ?? 0) }}" placeholder="Contoh: 6000000">
        </div>
        <div>
            <div class="muted">Biaya Operasional</div>
            <input type="text" name="total_operasional" value="{{ request('total_operasional', $ledger['total_operasional'] ?? 0) }}" placeholder="Contoh: 1500000">
        </div>
        <button type="submit" class="btn-green">Filter</button>
    </form>

    <div class="cards" style="margin-top:12px;">
        <div class="card"><div class="muted">Saldo Awal</div><h3>Rp {{ number_format($ledger['saldo_awal'], 0, ',', '.') }}</h3></div>
        <div class="card"><div class="muted">Total Penerimaan</div><h3>Rp {{ number_format($ledger['total_penerimaan'], 0, ',', '.') }}</h3></div>
        <div class="card"><div class="muted">Total Penyaluran</div><h3>Rp {{ number_format($ledger['total_penyaluran'], 0, ',', '.') }}</h3></div>
        <div class="card"><div class="muted">Total Operasional</div><h3>Rp {{ number_format($ledger['total_operasional'], 0, ',', '.') }}</h3></div>
        <div class="card"><div class="muted">Surplus / (Defisit)</div><h3>Rp {{ number_format($ledger['surplus_defisit'], 0, ',', '.') }}</h3></div>
        <div class="card"><div class="muted">Saldo Akhir</div><h3>Rp {{ number_format($ledger['saldo_akhir'], 0, ',', '.') }}</h3></div>
    </div>

    <div class="table-wrap" style="margin-top:14px;">
        <table>
            <thead><tr><th>Keterangan</th><th>Jumlah (Rp)</th></tr></thead>
            <tbody>
                <tr><td>Total Penerimaan Donasi</td><td>{{ number_format($ledger['total_penerimaan'], 0, ',', '.') }}</td></tr>
                <tr><td>Total Penyaluran Dana</td><td>({{ number_format($ledger['total_penyaluran'], 0, ',', '.') }})</td></tr>
                <tr><td>Biaya Operasional</td><td>({{ number_format($ledger['total_operasional'], 0, ',', '.') }})</td></tr>
                <tr><td><strong>Surplus / (Defisit)</strong></td><td><strong>{{ number_format($ledger['surplus_defisit'], 0, ',', '.') }}</strong></td></tr>
                <tr><td>Saldo Awal</td><td>{{ number_format($ledger['saldo_awal'], 0, ',', '.') }}</td></tr>
                <tr><td><strong>Saldo Akhir</strong></td><td><strong>{{ number_format($ledger['saldo_akhir'], 0, ',', '.') }}</strong></td></tr>
            </tbody>
        </table>
    </div>

    <p class="muted" style="margin-top:10px;">Catatan: Penyaluran dan operasional saat ini diinput manual per periode karena modul pengeluaran belum tersedia di database.</p>

    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a class="btn btn-green" href="{{ route('reports.donations.pdf', request()->query()) }}">Export PDF Resmi</a>
        <a class="btn" href="{{ route('reports.donations.excel', request()->query()) }}">Export Excel</a>
        <a class="btn btn-secondary" href="{{ route('reports.donations.print', request()->query()) }}" target="_blank">Print</a>
    </div>
</div>

<div class="grid-2" style="margin-top:14px;">
    <div class="card">
        <h3>Komposisi Donasi per Kategori</h3>
        <p class="muted" style="margin:0 0 8px;">Data chart mengikuti filter laporan (termasuk kategori "Tanpa Kategori" jika ada).</p>
        <div class="chart-wrap">
            <canvas
                data-chart="category-breakdown"
                data-labels='@json($categoryLabels)'
                data-values='@json($categoryValues)'></canvas>
        </div>
    </div>
    <div class="card">
        <h3>Komposisi Donasi per Metode</h3>
        <div class="chart-wrap">
            <canvas
                data-chart="donation-method-breakdown"
                data-labels='@json($methodLabels)'
                data-values='@json($methodValues)'></canvas>
        </div>
    </div>
</div>

<div class="table-wrap" style="margin-top:14px;">
<h3 style="padding:12px 12px 0;">Detail Penerimaan Donasi</h3>
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
