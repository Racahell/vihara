@extends('layouts.app')
@section('title', 'Instruksi Transfer Donasi')
@section('content')
<div class="card page-head">
    <h2>Instruksi Pembayaran Donasi</h2>
    <p class="muted">Silakan transfer sesuai nominal tepat agar verifikasi lebih cepat.</p>
    @if (session('status'))
        <div class="alert alert-success" style="margin-top:10px;">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error" style="margin-top:10px;">{{ $errors->first() }}</div>
    @endif
</div>

<div class="grid-2" style="margin-top:14px;">
    <div class="card">
        <h3>Detail Donasi</h3>
        <div style="display:grid;gap:8px;">
            <div><strong>Kode Donasi:</strong> DON-{{ str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div><strong>Nama Donatur:</strong> {{ $donation->donor_name }}</div>
            <div><strong>Nominal:</strong> Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>
            <div><strong>Status:</strong> {{ strtoupper($donation->verification_status) }}</div>
            <div><strong>Metode:</strong> {{ strtoupper($transferChannel === 'qris' ? 'QRIS' : 'TRANSFER REKENING') }}</div>
        </div>
    </div>

    <div class="card">
        @if($transferChannel === 'bank_transfer')
            <h3>Tujuan Transfer Rekening</h3>
            <div style="display:grid;gap:8px;">
                <div><strong>Bank:</strong> {{ $bank['bank_name'] }}</div>
                <div><strong>No. Rekening:</strong> {{ $bank['account_number'] }}</div>
                <div><strong>Atas Nama:</strong> {{ $bank['account_holder'] }}</div>
            </div>
            <p class="muted" style="margin-top:10px;">Setelah transfer berhasil, lanjutkan upload bukti transfer pada form di bawah.</p>
        @else
            <h3>Pembayaran QRIS</h3>
            <p class="muted" style="margin-top:10px;">Scan QR berikut untuk bayar otomatis sesuai nominal. Tidak perlu upload bukti transfer manual.</p>
            <p class="muted" style="margin-top:6px;"><strong>Catatan:</strong> Transaksi QRIS otomatis gagal jika tidak dibayar dalam 15 menit.</p>
            @if(!empty($qrisExpiredAt))
                <div class="muted" style="margin-top:8px;"><strong>Berlaku sampai:</strong> {{ \Illuminate\Support\Carbon::parse($qrisExpiredAt)->format('d-m-Y H:i:s') }}</div>
            @endif
        @endif
        <img
            @if(!empty($qrisImage))
                src="{{ $qrisImage }}"
            @elseif($qrDataUri)
                src="{{ $qrDataUri }}"
            @else
                data-qr-payload="{{ $qrPayload }}"
            @endif
            alt="QR Pembayaran Donasi"
            style="width:220px;height:220px;border:1px solid #e2e8f0;border-radius:10px;">
    </div>
</div>

@if($transferChannel === 'bank_transfer')
    <div class="card" style="margin-top:14px;">
        <h3>Upload Bukti Transfer (Wajib)</h3>
        <form action="{{ route('umat.donations.upload-proof', $donation) }}" method="POST" enctype="multipart/form-data" class="form-grid">
            @csrf
            <div>
                <label for="user-proof-file">File Bukti (jpg/jpeg/png/pdf)</label>
                <input id="user-proof-file" type="file" name="transfer_proof" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
            <button type="submit" class="btn btn-green btn-upload-proof">Saya Sudah Transfer & Upload Bukti</button>
        </form>
    </div>
@endif

<div class="card" style="margin-top:14px;">
    <h3>Langkah Verifikasi</h3>
    <ol style="margin:0;padding-left:18px;line-height:1.9;">
        <li>Transfer sesuai nominal tepat.</li>
        <li>Simpan bukti transfer.</li>
        <li>Admin memverifikasi dan menerbitkan kwitansi.</li>
    </ol>
    <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
        @if($isGuest)
            <a href="{{ route('guest.home') }}" class="btn btn-secondary">Kembali ke Guest</a>
            <a href="{{ route('login') }}" class="btn btn-primary">Login untuk Lihat Riwayat</a>
        @else
            <a href="{{ route('umat.donations.index') }}" class="btn btn-secondary">Kembali ke Donasi</a>
            <a href="{{ route('umat.my-history') }}" class="btn btn-primary">Lihat Riwayat Saya</a>
        @endif
    </div>
</div>
@endsection
