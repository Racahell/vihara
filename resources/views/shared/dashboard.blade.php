@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="cards dashboard-kpi-grid">
    <div class="card kpi-card kpi-info"><div class="kpi-label">Kegiatan Aktif</div><h2 class="kpi-value">{{ number_format($stats['kegiatan_aktif'], 0, ',', '.') }}</h2></div>
    <div class="card kpi-card kpi-info"><div class="kpi-label">Total Pendaftaran</div><h2 class="kpi-value">{{ number_format($stats['pendaftaran_total'], 0, ',', '.') }}</h2></div>
    <div class="card kpi-card kpi-info"><div class="kpi-label">Hadir Hari Ini</div><h2 class="kpi-value">{{ number_format($stats['hadir_hari_ini'], 0, ',', '.') }}</h2></div>
    @if($isPetugas ?? false)
        <div class="card kpi-card kpi-info"><div class="kpi-label">Total Umat Aktif</div><h2 class="kpi-value">{{ number_format($stats['umat_aktif'] ?? 0, 0, ',', '.') }}</h2></div>
        <div class="card kpi-card kpi-success"><div class="kpi-label">Check-In Online (Hari Ini)</div><h2 class="kpi-value">{{ number_format($stats['checkin_online_hari_ini'] ?? 0, 0, ',', '.') }}</h2></div>
        <div class="card kpi-card kpi-warning"><div class="kpi-label">Walk-In (Hari Ini)</div><h2 class="kpi-value">{{ number_format($stats['walkin_hari_ini'] ?? 0, 0, ',', '.') }}</h2></div>
    @else
        <div class="card kpi-card kpi-success kpi-primary"><div class="kpi-label">Donasi Approved</div><h2 class="kpi-value">Rp {{ number_format($stats['donasi_berhasil'], 0, ',', '.') }}</h2></div>
    @endif
</div>

@if($canSeeIncomeReport ?? false)
    <div class="cards dashboard-kpi-grid">
        <div class="card kpi-card kpi-success"><div class="kpi-label">Income Daily</div><h2 class="kpi-value">Rp {{ number_format($incomeReport['daily'] ?? 0, 0, ',', '.') }}</h2></div>
        <div class="card kpi-card kpi-primary kpi-success"><div class="kpi-label">Income Today</div><h2 class="kpi-value">Rp {{ number_format($incomeReport['today'] ?? 0, 0, ',', '.') }}</h2></div>
        <div class="card kpi-card kpi-info"><div class="kpi-label">Income Yesterday</div><h2 class="kpi-value">Rp {{ number_format($incomeReport['yesterday'] ?? 0, 0, ',', '.') }}</h2></div>
    </div>
@endif

@if($canSeeDonationWidgets ?? false)
    <div class="grid-2" style="margin-top:14px;">
        <div class="card">
            <div class="table-toolbar" style="margin:0 0 8px 0;">
                <h3 style="margin:0;">Tren Donasi Bulanan</h3>
                <div class="table-length">
                    <label for="chart-type-monthly">Diagram</label>
                    <select id="chart-type-monthly" data-chart-type data-chart-target="donation-monthly">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                        <option value="pie">Pie</option>
                        <option value="doughnut">Doughnut</option>
                    </select>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas
                    data-chart="donation-monthly"
                    data-unit="currency"
                    data-dataset-label="Donasi Bulanan (Rp)"
                    data-labels='@json($monthlyDonationLabels)'
                    data-values='@json($monthlyDonationValues)'></canvas>
            </div>
        </div>
        <div class="card">
            <div class="table-toolbar" style="margin:0 0 8px 0;">
                <h3 style="margin:0;">Komposisi Donasi per Kategori</h3>
                <div class="table-length">
                    <label for="chart-type-category">Diagram</label>
                    <select id="chart-type-category" data-chart-type data-chart-target="category-breakdown">
                        <option value="doughnut">Doughnut</option>
                        <option value="pie">Pie</option>
                        <option value="bar">Bar</option>
                        <option value="line">Line</option>
                    </select>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas
                    data-chart="category-breakdown"
                    data-unit="currency"
                    data-dataset-label="Total Donasi per Kategori (Rp)"
                    data-labels='@json($categoryLabels)'
                    data-values='@json($categoryValues)'></canvas>
            </div>
        </div>
    </div>
@endif

@if($isPetugas ?? false)
    <div class="grid-2" style="margin-top:14px;">
        <div class="card">
            <div class="table-toolbar" style="margin:0 0 8px 0;">
                <h3 style="margin:0;">Tren Check-In 7 Hari</h3>
                <div class="table-length">
                    <label for="chart-type-checkin-daily">Diagram</label>
                    <select id="chart-type-checkin-daily" data-chart-type data-chart-target="checkin-daily">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                        <option value="pie">Pie</option>
                        <option value="doughnut">Doughnut</option>
                    </select>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas
                    data-chart="checkin-daily"
                    data-dataset-label="Jumlah Check-In"
                    data-unit="count"
                    data-labels='@json($petugasDailyLabels ?? [])'
                    data-values='@json($petugasDailyValues ?? [])'></canvas>
            </div>
        </div>

        <div class="card">
            <div class="table-toolbar" style="margin:0 0 8px 0;">
                <h3 style="margin:0;">Komposisi Metode Hari Ini</h3>
                <div class="table-length">
                    <label for="chart-type-checkin-method">Diagram</label>
                    <select id="chart-type-checkin-method" data-chart-type data-chart-target="checkin-method">
                        <option value="doughnut">Doughnut</option>
                        <option value="pie">Pie</option>
                        <option value="bar">Bar</option>
                        <option value="line">Line</option>
                    </select>
                </div>
            </div>
            <div class="chart-wrap">
                <canvas
                    data-chart="checkin-method"
                    data-dataset-label="Jumlah Peserta"
                    data-unit="count"
                    data-labels='@json($petugasMethodLabels ?? [])'
                    data-values='@json($petugasMethodValues ?? [])'></canvas>
            </div>
        </div>
    </div>
@endif

@if(($canSeeDonationWidgets ?? false) && ($recentApprovedDonations ?? collect())->isNotEmpty())
    <div class="card" style="margin-top:14px;">
        <h3>Aktivitas Donasi Terbaru</h3>
        <div class="table-wrap" style="margin-top:10px;">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Kode</th>
                        <th>Donatur</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentApprovedDonations as $donation)
                        <tr>
                            <td>{{ $donation->donated_at?->format('d-m-Y H:i') ?? '-' }}</td>
                            <td>DON-{{ str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $donation->donor_name ?: '-' }}</td>
                            <td>Rp {{ number_format((int) $donation->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
