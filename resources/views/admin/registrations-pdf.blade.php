<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2a2e; font-size: 12px; }
        .head { border-bottom: 2px solid #d9e8fb; padding-bottom: 8px; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 700; }
        .meta { color: #4f6066; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #dce6ea; padding: 6px; }
        th { background: #f4f8ff; text-align: left; }
    </style>
</head>
<body>
    <div class="head">
        <div class="title">Laporan Pendaftaran Kegiatan</div>
        <div class="meta">Tanggal cetak: {{ $printedAt->format('d-m-Y H:i') }}</div>
        <div class="meta">Periode: {{ $period['start'] ?: '-' }} s/d {{ $period['end'] ?: '-' }}</div>
        <div class="meta">Kegiatan: {{ $activityTitle }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
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
            @forelse($registrations as $index => $reg)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $reg->registration_code }}</td>
                    <td>{{ $reg->participant_name }}</td>
                    <td>{{ $reg->participant_phone ?? '-' }}</td>
                    <td>{{ $reg->activity->title ?? '-' }}</td>
                    <td>{{ strtoupper($reg->registration_type) }}</td>
                    <td>{{ strtoupper($reg->attendance_status) }}</td>
                    <td>{{ $reg->registered_at?->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Tidak ada data pendaftaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

