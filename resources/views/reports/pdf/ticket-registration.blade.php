<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2a2e; font-size: 12px; }
        .ticket { border: 2px solid #f2c3d6; border-radius: 12px; padding: 14px; }
        .head { margin-bottom: 10px; }
        .title { font-size: 16px; font-weight: 700; }
        .muted { color: #4f6066; }
        .grid { width: 100%; }
        .grid td { vertical-align: top; }
        .qr { width: 220px; height: 220px; border: 1px solid #dce6ea; border-radius: 8px; }
        .code { margin-top: 8px; font-weight: 700; font-size: 14px; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="head">
            <div class="title">Tiket Kegiatan Vihara</div>
            <div class="muted">Tanggal cetak: {{ $printedAt->format('d-m-Y H:i') }}</div>
        </div>

        <table class="grid">
            <tr>
                <td style="width:230px;">
                    <img src="{{ $qrDataUri }}" class="qr" alt="QR Tiket">
                    <div class="code">{{ $registration->registration_code }}</div>
                </td>
                <td>
                    <div><strong>Nama Peserta:</strong> {{ $registration->participant_name }}</div>
                    <div><strong>No HP:</strong> {{ $registration->participant_phone ?: '-' }}</div>
                    <div><strong>Kegiatan:</strong> {{ $registration->activity->title ?? '-' }}</div>
                    <div><strong>Lokasi:</strong> {{ $registration->activity->location ?? 'Vihara' }}</div>
                    <div><strong>Waktu:</strong> {{ $registration->activity->start_at?->format('d-m-Y H:i') ?? '-' }}</div>
                    <div><strong>Status:</strong> {{ strtoupper($registration->attendance_status) }}</div>
                    <p class="muted" style="margin-top:12px;">Tunjukkan QR ini saat check-in di vihara.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>

