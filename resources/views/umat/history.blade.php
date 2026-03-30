@extends('layouts.app')
@section('title', 'Riwayat Saya')
@section('content')
<div class="grid-2">
    <div class="card">
        <h3>Riwayat Pendaftaran</h3>
        @forelse($registrations as $reg)
            <div class="qr-ticket">
                <img alt="QR tiket" data-qr-payload="{{ $reg->qr_payload }}">
                <div>
                    <strong>{{ $reg->activity->title ?? '-' }}</strong>
                    <div class="muted">{{ $reg->registration_code }} | {{ strtoupper($reg->attendance_status) }}</div>
                    <div class="muted">Tunjukkan QR ini saat check-in di vihara.</div>
                    <div style="margin-top:8px;">
                        <a class="btn btn-secondary" href="{{ route('umat.my-history.ticket-pdf', $reg) }}">Unduh QR ke PDF</a>
                    </div>
                </div>
            </div>
        @empty
            <p class="muted">Belum ada pendaftaran kegiatan.</p>
        @endforelse
    </div>
    <div class="card">
        <h3>Riwayat Donasi</h3>
        @forelse($donations as $donation)
            <div style="padding:8px 0;border-bottom:1px dashed #e8eef0;">
                <strong>Rp {{ number_format($donation->amount, 0, ',', '.') }}</strong>
                <div class="muted">{{ strtoupper($donation->payment_status) }} / {{ strtoupper($donation->verification_status) }}</div>
                @if($donation->receipt_number)
                    <div class="muted">Kwitansi: {{ $donation->receipt_number }}</div>
                @endif
            </div>
        @empty
            <p class="muted">Belum ada data donasi.</p>
        @endforelse
    </div>
</div>
@endsection
