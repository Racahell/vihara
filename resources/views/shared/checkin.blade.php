@extends('layouts.app')
@section('title', 'Check In')
@section('content')
<div class="grid-2">
    <div class="card">
        <h3>Check-In dengan Kode</h3>
        <form id="checkin-code-form" action="{{ route('shared.checkin.by-code') }}" method="POST" class="form-grid">
            @csrf
            <input id="registration_code" type="text" name="registration_code" placeholder="Contoh: REG-260330-ABC123" required>
            <button type="submit" class="btn-green">Proses Check-In Manual</button>
        </form>
        <div class="muted" id="scan-result" style="margin-top:8px;">Jika QR terbaca, check-in diproses otomatis.</div>
    </div>
    <div class="card">
        <h3>Scan QR Kamera</h3>
        <div id="qr-reader"></div>
        <div style="display:flex;gap:8px;margin-top:10px;">
            <button type="button" id="start-scan-btn" class="btn-green">Mulai Scanner</button>
            <button type="button" id="stop-scan-btn">Stop</button>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-top:14px;">
    <div class="card">
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
            <input type="text" name="participant_phone" placeholder="No HP">
            <button type="submit">Simpan Walk-In</button>
        </form>
    </div>
</div>

<div class="table-wrap" style="margin-top:14px;">
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>ID Registrasi</th>
                <th>Kegiatan</th>
                <th>Metode</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todayLogs as $log)
                <tr>
                    <td>{{ $log->checked_in_at?->format('d-m-Y H:i') }}</td>
                    <td>#{{ $log->activity_registration_id }}</td>
                    <td>#{{ $log->activity_id }}</td>
                    <td>{{ strtoupper($log->method) }}</td>
                    <td>{{ $log->handled_by ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada log check-in.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
