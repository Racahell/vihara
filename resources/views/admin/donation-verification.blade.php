@extends('layouts.app')
@section('title', 'Verifikasi Donasi')
@section('content')
<div class="table-wrap">
<table>
    <thead><tr><th>ID</th><th>Donatur</th><th>Nominal</th><th>Pembayaran</th><th>Bukti</th><th>Verifikasi</th><th>Aksi</th></tr></thead>
    <tbody>
    @foreach($donations as $donation)
        <tr>
            <td>#{{ $donation->id }}</td>
            <td>{{ $donation->donor_name }}</td>
            <td>Rp {{ number_format($donation->amount, 0, ',', '.') }}</td>
            <td>
                {{ strtoupper((string) data_get($donation->payment_payload, 'channel', $donation->payment_method)) }}<br>
                <span class="muted">{{ strtoupper($donation->payment_status) }}</span>
            </td>
            <td>
                @if($donation->bank_transfer_proof_path)
                    <a class="btn btn-secondary" href="{{ route('admin.donation-proof.download', $donation) }}">Unduh Bukti</a>
                @else
                    <span class="muted">Belum upload</span>
                @endif
            </td>
            <td>{{ strtoupper($donation->verification_status) }}</td>
            <td>
                <form action="{{ route('admin.donation-verification.verify', $donation) }}" method="POST" style="display:flex;gap:8px;align-items:center;">
                    @csrf
                    <select name="action">
                        <option value="approve">Approve</option>
                        <option value="reject">Reject</option>
                    </select>
                    <input type="text" name="reason" placeholder="Alasan reject (opsional)">
                    <button type="submit">Proses</button>
                </form>
                @if($donation->receipt_pdf_path)
                    <a class="btn btn-green" style="display:inline-block;margin-top:8px;" href="{{ route('admin.donation-receipts.download', $donation) }}">Unduh Kwitansi</a>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
{{ $donations->links() }}
@endsection
