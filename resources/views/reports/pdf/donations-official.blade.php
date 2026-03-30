<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2a2e; font-size: 12px; }
        .head { border-bottom: 2px solid #f2c3d6; padding-bottom: 8px; margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 700; }
        .meta { color: #4f6066; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #dce6ea; padding: 6px; }
        th { background: #fdf3f8; text-align: left; }
        .summary { margin-top: 12px; font-size: 12px; }
        .summary div { margin-top: 3px; }
        .sign { margin-top: 24px; width: 100%; }
        .sign td { border: 0; vertical-align: top; }
    </style>
</head>
<body>
    <div class="head">
        <div class="title">Laporan Donasi Resmi - Vihara</div>
        <div class="meta">Tanggal cetak: {{ $printedAt->format('d-m-Y H:i') }}</div>
        <div class="meta">Periode: {{ $period['start'] ?: '-' }} s/d {{ $period['end'] ?: '-' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Donatur</th>
                <th>Kategori</th>
                <th>Metode</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Kwitansi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($donations as $index => $donation)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $donation->donated_at?->format('d-m-Y') }}</td>
                    <td>DON-{{ str_pad((string)$donation->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $donation->donor_name }}</td>
                    <td>{{ $donation->category->name ?? '-' }}</td>
                    <td>{{ strtoupper((string) data_get($donation->payment_payload, 'channel', $donation->payment_method)) }}</td>
                    <td>Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
                    <td>{{ strtoupper($donation->verification_status) }}</td>
                    <td>{{ $donation->receipt_number ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div><strong>Total Donasi Masuk:</strong> Rp {{ number_format($summary['total_masuk'], 0, ',', '.') }}</div>
        <div><strong>Donasi Terverifikasi:</strong> Rp {{ number_format($summary['total_terverifikasi'], 0, ',', '.') }}</div>
        <div><strong>Donasi Pending:</strong> Rp {{ number_format($summary['total_pending'], 0, ',', '.') }}</div>
    </div>

    <table class="sign">
        <tr>
            <td></td>
            <td style="text-align:center; width:220px;">
                Mengetahui,<br>Ketua / Owner<br><br><br><br>
                (____________________)
            </td>
        </tr>
    </table>
</body>
</html>
