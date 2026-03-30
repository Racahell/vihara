<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2a2e; font-size: 12px; }
        .box { border: 1px solid #dce6ea; border-radius: 8px; padding: 14px; }
        .title { font-size: 17px; font-weight: 700; color: #2f4050; }
        .num { margin-top: 4px; color: #5b6e76; }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        td { padding: 5px 0; }
        .amount { font-size: 18px; font-weight: 700; color: #d05f90; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="box">
        <div class="title">KWITANSI DONASI RESMI</div>
        <div class="num">No: {{ $receiptNumber }}</div>

        <table>
            <tr><td width="120">Nama Donatur</td><td>: {{ $donation->donor_name }}</td></tr>
            <tr><td>Tanggal</td><td>: {{ $verifiedAt->format('d-m-Y H:i') }}</td></tr>
            <tr><td>Kategori</td><td>: {{ $donation->category->name ?? '-' }}</td></tr>
            <tr><td>Metode</td><td>: {{ strtoupper($donation->payment_method) }}</td></tr>
        </table>

        <div class="amount">Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>

        <table style="margin-top:24px;">
            <tr>
                <td></td>
                <td style="text-align:center; width:220px;">
                    Disahkan oleh,<br>{{ $approver->name }}<br><br><br>
                    (____________________)
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
