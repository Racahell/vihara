<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Pendaftaran Kegiatan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #1f2937; }
        h1 { margin: 0 0 6px; font-size: 22px; }
        .meta { color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 7px; font-size: 12px; }
        th { background: #f3f4f6; text-align: left; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:12px;">
        <button onclick="window.print()">Print Sekarang</button>
    </div>

    <h1>Laporan Pendaftaran Kegiatan</h1>
    <div class="meta">
        Periode: {{ $period['start'] ?: '-' }} s/d {{ $period['end'] ?: '-' }}<br>
        Kegiatan: {{ $activityTitle }}<br>
        Dicetak: {{ $printedAt->format('d-m-Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Peserta</th>
                <th>HP</th>
                <th>Kegiatan</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Waktu Daftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($registrations as $reg)
                <tr>
                    <td>{{ $reg->registration_code }}</td>
                    <td>{{ $reg->participant_name }}</td>
                    <td>{{ $reg->participant_phone ?? '-' }}</td>
                    <td>{{ $reg->activity->title ?? '-' }}</td>
                    <td>{{ strtoupper($reg->registration_type) }}</td>
                    <td>{{ strtoupper($reg->attendance_status) }}</td>
                    <td>{{ $reg->registered_at?->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada data pendaftaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

