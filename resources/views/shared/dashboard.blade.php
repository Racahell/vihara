@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="cards">
    <div class="card"><div class="muted">Kegiatan Aktif</div><h2>{{ $stats['kegiatan_aktif'] }}</h2></div>
    <div class="card"><div class="muted">Total Pendaftaran</div><h2>{{ $stats['pendaftaran_total'] }}</h2></div>
    <div class="card"><div class="muted">Hadir Hari Ini</div><h2>{{ $stats['hadir_hari_ini'] }}</h2></div>
    @if($isPetugas ?? false)
        <div class="card"><div class="muted">Total Umat Aktif</div><h2>{{ $stats['umat_aktif'] ?? 0 }}</h2></div>
        <div class="card"><div class="muted">Check-In Online (Hari Ini)</div><h2>{{ $stats['checkin_online_hari_ini'] ?? 0 }}</h2></div>
        <div class="card"><div class="muted">Walk-In (Hari Ini)</div><h2>{{ $stats['walkin_hari_ini'] ?? 0 }}</h2></div>
    @else
        <div class="card"><div class="muted">Donasi Approved</div><h2>Rp {{ number_format($stats['donasi_berhasil'], 0, ',', '.') }}</h2></div>
    @endif
</div>

@if($canSeeIncomeReport ?? false)
    <div class="cards">
        <div class="card"><div class="muted">Income DAILY</div><h2>Rp {{ number_format($incomeReport['daily'] ?? 0, 0, ',', '.') }}</h2></div>
        <div class="card"><div class="muted">Income TODAY</div><h2>Rp {{ number_format($incomeReport['today'] ?? 0, 0, ',', '.') }}</h2></div>
        <div class="card"><div class="muted">Income YESTERDAY</div><h2>Rp {{ number_format($incomeReport['yesterday'] ?? 0, 0, ',', '.') }}</h2></div>
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
@endsection
