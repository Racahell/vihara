<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Laporan Donasi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #1f2937; }
        h1 { margin: 0 0 6px; font-size: 22px; }
        .meta { color: #6b7280; margin-bottom: 16px; }
        .summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin-bottom: 14px; }
        .summary .box { border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; }
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

    <h1>Laporan Keuangan Donasi Vihara</h1>
    <div class="meta">
        Periode: {{ $period['start'] ?: '-' }} s/d {{ $period['end'] ?: '-' }}<br>
        Dicetak: {{ $printedAt->format('d-m-Y H:i') }}
    </div>

    <div class="summary">
        <div class="box"><strong>Total Donasi Masuk</strong><br>Rp {{ number_format($summary['total_masuk'], 0, ',', '.') }}</div>
        <div class="box"><strong>Donasi Terverifikasi</strong><br>Rp {{ number_format($summary['total_terverifikasi'], 0, ',', '.') }}</div>
        <div class="box"><strong>Donasi Pending</strong><br>Rp {{ number_format($summary['total_pending'], 0, ',', '.') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Donasi</th>
                <th>Donatur</th>
                <th>Kategori</th>
                <th>Metode</th>
                <th>Nominal</th>
                <th>Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $donation)
                <tr>
                    <td>{{ $donation->donated_at?->format('d-m-Y') }}</td>
                    <td>DON-{{ str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $donation->donor_name }}</td>
                    <td>{{ $donation->category->name ?? '-' }}</td>
                    <td>{{ strtoupper((string) data_get($donation->payment_payload, 'channel', $donation->payment_method)) }}</td>
                    <td>Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
                    <td>{{ strtoupper($donation->verification_status) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada data donasi.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

