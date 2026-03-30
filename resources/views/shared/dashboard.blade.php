@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="cards">
    <div class="card"><div class="muted">Kegiatan Aktif</div><h2>{{ $stats['kegiatan_aktif'] }}</h2></div>
    <div class="card"><div class="muted">Total Pendaftaran</div><h2>{{ $stats['pendaftaran_total'] }}</h2></div>
    <div class="card"><div class="muted">Hadir Hari Ini</div><h2>{{ $stats['hadir_hari_ini'] }}</h2></div>
    <div class="card"><div class="muted">Donasi Approved</div><h2>Rp {{ number_format($stats['donasi_berhasil'], 0, ',', '.') }}</h2></div>
</div>
<div class="card">
    <h3>Ringkasan Sistem</h3>
    <p class="muted">Alur utama: Umat daftar -> daftar kegiatan -> hadir/check-in -> donasi -> verifikasi admin -> laporan.</p>
</div>

<div class="grid-2" style="margin-top:14px;">
    <div class="card">
        <h3>Tren Donasi Bulanan</h3>
        <div class="chart-wrap">
            <canvas
                data-chart="donation-monthly"
                data-labels='@json($monthlyDonationLabels)'
                data-values='@json($monthlyDonationValues)'></canvas>
        </div>
    </div>
    <div class="card">
        <h3>Komposisi Donasi per Kategori</h3>
        <div class="chart-wrap">
            <canvas
                data-chart="category-breakdown"
                data-labels='@json($categoryLabels)'
                data-values='@json($categoryValues)'></canvas>
        </div>
    </div>
</div>
@endsection
