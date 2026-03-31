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
                <button type="button" class="btn btn-outline" data-modal-open="donation-modal-{{ $donation->id }}">Detail</button>
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

@foreach($donations as $donation)
    <div class="modal" id="donation-modal-{{ $donation->id }}" aria-hidden="true">
        <div class="modal-backdrop" data-modal-close="donation-modal-{{ $donation->id }}"></div>
        <div class="modal-dialog">
            <div class="modal-header">
                <div>
                    <h3>Detail Verifikasi Donasi</h3>
                    <div class="muted">Donasi #{{ $donation->id }}</div>
                </div>
                <button type="button" class="btn btn-secondary" data-modal-close="donation-modal-{{ $donation->id }}">Tutup</button>
            </div>

            <div class="modal-body">
                <div class="detail-section">
                    <h4>Informasi Donasi</h4>
                    <div class="detail-grid">
                        <div><strong>Donatur</strong><div>{{ $donation->donor_name }}</div></div>
                        <div><strong>Email</strong><div>{{ $donation->donor_email ?: '-' }}</div></div>
                        <div><strong>No HP</strong><div>{{ $donation->donor_phone ?: '-' }}</div></div>
                        <div><strong>Nominal</strong><div>Rp {{ number_format($donation->amount, 0, ',', '.') }}</div></div>
                        <div><strong>Channel</strong><div>{{ strtoupper((string) data_get($donation->payment_payload, 'channel', $donation->payment_method)) }}</div></div>
                        <div><strong>Status Pembayaran</strong><div>{{ strtoupper($donation->payment_status) }}</div></div>
                        <div><strong>Status Verifikasi</strong><div>{{ strtoupper($donation->verification_status) }}</div></div>
                        <div><strong>Catatan</strong><div>{{ $donation->note ?: '-' }}</div></div>
                    </div>
                </div>

                @if($donation->bank_transfer_proof_path)
                    <div class="detail-section">
                        <a class="btn btn-secondary" href="{{ route('admin.donation-proof.download', $donation) }}">Unduh Bukti Transfer</a>
                    </div>
                @endif

                @if($donation->verification_status === 'pending')
                    <div class="detail-section">
                        <h4>Aksi Verifikasi</h4>
                        <form action="{{ route('admin.donation-verification.verify', $donation) }}" method="POST" class="form-grid">
                            @csrf
                            <div>
                                <label for="reason-{{ $donation->id }}">Alasan (opsional)</label>
                                <input id="reason-{{ $donation->id }}" type="text" name="reason" placeholder="Alasan reject atau catatan approve">
                            </div>
                            <div class="modal-footer-actions modal-footer-actions-split">
                                <button class="btn btn-green" type="submit" name="action" value="approve">Approve</button>
                                <div class="modal-footer-right">
                                    <button class="btn btn-danger" type="submit" name="action" value="reject">Reject</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="detail-section">
                        <p class="muted" style="margin:0;">Donasi ini sudah diverifikasi. Tidak ada aksi lanjutan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach
@endsection
