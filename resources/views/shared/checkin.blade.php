@extends('layouts.app')
@section('title', 'Check In')
@section('content')
<div class="checkin-page">
<div class="grid-2 checkin-top-grid">
    <div class="card checkin-card">
        <h3>Check-In dengan Kode</h3>
        <form id="checkin-code-form" action="{{ route('shared.checkin.by-code') }}" method="POST" class="form-grid">
            @csrf
            <input id="registration_code" type="text" name="registration_code" placeholder="Contoh: REG-260330-ABC123" required>
            <button type="submit" class="btn-green">Proses Check-In Manual</button>
        </form>
        <div class="muted" id="scan-result" style="margin-top:8px;">Jika QR terbaca, check-in diproses otomatis.</div>
    </div>
    <div class="card checkin-card">
        <h3>Scan QR Kamera</h3>
        <div id="qr-reader"></div>
        <div class="checkin-scan-actions">
            <button type="button" id="start-scan-btn" class="btn-green">Mulai Scanner</button>
            <button type="button" id="stop-scan-btn" class="btn-secondary">Stop</button>
        </div>
    </div>
</div>

<div class="card checkin-card checkin-walkin-card">
    <h3>Walk-In</h3>
    <form action="{{ route('shared.checkin.walkin') }}" method="POST" class="form-grid">
        @csrf
        <select name="activity_id" required>
            <option value="">Pilih Kegiatan</option>
            @foreach($activities as $activity)
                <option value="{{ $activity->id }}">{{ $activity->title }} - {{ $activity->start_at->format('d M Y H:i') }}</option>
            @endforeach
        </select>
        <input type="text" name="participant_name" placeholder="Nama peserta" required>
        <input type="number" name="participant_age" min="0" max="120" placeholder="Usia" required>
        <select name="participant_gender" required>
            <option value="">Pilih Jenis Kelamin</option>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select>
        <input type="text" name="participant_address" placeholder="Alamat peserta" required>
        <button type="submit" class="btn-green">Simpan Walk-In</button>
    </form>
</div>
</div>
@endsection
