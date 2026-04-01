<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instruksi Donasi Guest</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="auth-page">
    <div class="auth-shell">
        <div class="auth-card auth-card-wide">
            <h2>Instruksi Pembayaran Donasi Guest</h2>
            <p class="muted">Silakan transfer sesuai nominal tepat agar verifikasi cepat.</p>
            @if (session('status'))
                <div class="alert alert-success" style="margin-top:10px;">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error" style="margin-top:10px;">{{ $errors->first() }}</div>
            @endif

            <div class="grid-2" style="margin-top:12px;">
                <div class="card">
                    <h3>Detail Donasi</h3>
                    <div style="display:grid;gap:8px;">
                        <div><strong>Kode Donasi:</strong> DON-{{ str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT) }}</div>
                        <div><strong>Nama Donatur:</strong> {{ $donation->donor_name }}</div>
                        <div><strong>Nominal:</strong> Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>
                        <div><strong>Metode:</strong> TRANSFER REKENING</div>
                    </div>
                </div>
                <div class="card">
                    <h3>Tujuan Transfer Rekening</h3>
                    <div style="display:grid;gap:8px;">
                        <div><strong>Bank:</strong> {{ $bank['bank_name'] }}</div>
                        <div><strong>No. Rekening:</strong> {{ $bank['account_number'] }}</div>
                        <div><strong>Atas Nama:</strong> {{ $bank['account_holder'] }}</div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top:12px;">
                <h3>Upload Bukti Transfer (Wajib)</h3>
                <form action="{{ route('guest.donations.upload-proof', $donation) }}" method="POST" enctype="multipart/form-data" class="form-grid">
                    @csrf
                    <input type="hidden" name="verification_key" value="{{ $verificationKey }}">
                    <div>
                        <label for="guest-proof-file">File Bukti (jpg/jpeg/png/pdf)</label>
                        <input id="guest-proof-file" type="file" name="transfer_proof" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                    <button type="submit" class="btn btn-green btn-upload-proof">Saya Sudah Transfer & Upload Bukti</button>
                </form>
            </div>

            <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('guest.home') }}" class="btn btn-secondary">Kembali ke Guest</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Daftar Akun</a>
                <a href="{{ route('login') }}" class="btn btn-green">Login</a>
            </div>
        </div>
    </div>

    @include('layouts.footer')
</div>
</body>
</html>
