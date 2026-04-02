<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2a2e; font-size: 12px; }
        .box { border: 1px solid #d9e0e5; border-radius: 8px; padding: 14px; }
        .title { font-size: 18px; font-weight: 700; text-align: center; margin-bottom: 8px; }
        .meta { width: 100%; border-collapse: collapse; margin-top: 2px; }
        .meta td { padding: 3px 0; }
        .divider { margin-top: 10px; border-top: 1px dashed #cfd8df; }
        .label { color: #5b6e76; width: 150px; }
        .amount-box { margin-top: 10px; border: 1px solid #dde3e8; border-radius: 6px; background: #f3f4f6; padding: 10px; }
        .amount-value { font-size: 20px; font-weight: 700; color: #1f2a2e; }
        .section-title { margin-top: 12px; font-weight: 700; }
        .status { display: inline-block; padding: 4px 8px; border-radius: 4px; background: #f3f4f6; font-weight: 700; }
        .thanks { margin-top: 14px; text-align: center; color: #4b5d66; }
        .sign { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .sign td { border: 0; }
    </style>
</head>
<body>
    @php
        $paymentChannel = strtoupper((string) data_get($donation->payment_payload, 'channel', $donation->payment_method));
        $tujuanDonasi = $donation->category->name ?? ($donation->activity->title ?? 'Donasi Umum');
        $statusLunas = strtolower((string) $donation->payment_status) === 'paid';
    @endphp
    <div class="box">
        <div class="title">KWITANSI DONASI</div>

        <table class="meta">
            <tr><td class="label">No. Kwitansi</td><td>: {{ $receiptNumber }}</td></tr>
            <tr><td class="label">Tanggal</td><td>: {{ $verifiedAt->format('d-m-Y H:i') }}</td></tr>
            <tr><td class="label">ID Donasi</td><td>: DON-{{ str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT) }}</td></tr>
            @if(!empty($donation->midtrans_transaction_id))
                <tr><td class="label">ID Transaksi</td><td>: {{ $donation->midtrans_transaction_id }}</td></tr>
            @endif
        </table>

        <div class="divider"></div>

        <table class="meta">
            <tr><td class="label">Nama Donatur</td><td>: {{ $donation->donor_name }}</td></tr>
            <tr><td class="label">Tujuan Donasi</td><td>: {{ $tujuanDonasi }}</td></tr>
            <tr><td class="label">Metode Pembayaran</td><td>: {{ $paymentChannel }}</td></tr>
            <tr><td class="label">Status</td><td>: <span class="status">{{ $statusLunas ? 'LUNAS' : strtoupper((string) $donation->payment_status) }}</span></td></tr>
        </table>

        <div class="section-title">Jumlah Donasi</div>
        <div class="amount-box">
            <div class="amount-value">Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>
        </div>

        <div class="thanks">
            Terima kasih atas donasi Anda.<br>
            Semoga menjadi amal kebaikan.
        </div>

        <table class="sign">
            <tr>
                <td></td>
                <td style="text-align:center; width:220px;">
                    Penerima,<br>{{ $organizationName ?? config('app.name') }}<br><br><br>
                    <strong>{{ $receiverName ?? ($approver->name ?? '-') }}</strong><br>
                    (____________________)
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
