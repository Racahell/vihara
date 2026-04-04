@extends('layouts.app')
@section('title', 'Riwayat Saya')
@section('content')
<div class="grid-2 history-grid">
    <div class="card history-card">
        <div class="history-head">
            <h3>Riwayat Pendaftaran</h3>
            <form method="GET" class="history-per-page-form">
                <input type="hidden" name="reg_page" value="1">
                <input type="hidden" name="don_page" value="1">
                <label for="history_per_page">Tampilkan</label>
                <select id="history_per_page" name="per_page" onchange="this.form.submit()">
                    @foreach([5, 10, 15, 20] as $option)
                        <option value="{{ $option }}" @selected(($perPage ?? 5) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="history-list">
            @forelse($registrations as $reg)
                <div class="qr-ticket">
                    <div class="qr-ticket-compact">
                        <strong>{{ $reg->activity->title ?? '-' }}</strong>
                        <div class="muted">{{ $reg->registration_code }}</div>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-secondary" data-modal-open="registration-modal-{{ $reg->id }}">Detail</button>
                        </div>
                    </div>
                </div>

                <div class="modal" id="registration-modal-{{ $reg->id }}" aria-hidden="true">
                    <div class="modal-backdrop" data-modal-close="registration-modal-{{ $reg->id }}"></div>
                    <div class="modal-dialog">
                        <div class="modal-header">
                            <strong>{{ $reg->activity->title ?? '-' }}</strong>
                            <button type="button" class="btn btn-secondary" data-modal-close="registration-modal-{{ $reg->id }}">Tutup</button>
                        </div>
                        <div class="modal-body">
                            <div class="detail-section">
                                <div><strong>Kode Registrasi:</strong> {{ $reg->registration_code }}</div>
                                <div><strong>Status:</strong> {{ strtoupper($reg->attendance_status) }}</div>
                                <div><strong>Nama Peserta:</strong> {{ $reg->participant_name }}</div>
                                <div><strong>Usia:</strong> {{ $reg->participant_age ?? '-' }}</div>
                                <div><strong>Jenis Kelamin:</strong> {{ $reg->participant_gender === 'L' ? 'Laki-laki' : ($reg->participant_gender === 'P' ? 'Perempuan' : '-') }}</div>
                                <div><strong>Alamat:</strong> {{ $reg->participant_address ?? '-' }}</div>
                            </div>
                            <div class="modal-footer-actions">
                                <a class="btn btn-secondary" href="{{ route('umat.my-history.ticket-pdf', $reg) }}">Unduh QR ke PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="muted">Belum ada pendaftaran kegiatan.</p>
            @endforelse
        </div>

        <div class="history-pagination">
            {{ $registrations->appends(['per_page' => $perPage, 'don_page' => request('don_page', 1)])->links() }}
        </div>
    </div>

    <div class="card history-card">
        <div class="history-head">
            <h3>Riwayat Donasi</h3>
            <form method="GET" class="history-per-page-form">
                <input type="hidden" name="reg_page" value="1">
                <input type="hidden" name="don_page" value="1">
                <label for="history_per_page_donation">Tampilkan</label>
                <select id="history_per_page_donation" name="per_page" onchange="this.form.submit()">
                    @foreach([5, 10, 15, 20] as $option)
                        <option value="{{ $option }}" @selected(($perPage ?? 5) === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="history-list">
            @forelse($donations as $donation)
                @php
                    $donationLabel = $donation->receipt_number
                        ? ('Kwitansi: ' . preg_replace('/\s+/', '', (string) $donation->receipt_number))
                        : ('Donasi #' . $donation->id);
                @endphp
                <div class="qr-ticket">
                    <div class="qr-ticket-compact">
                        <strong>Rp {{ number_format($donation->amount, 0, ',', '.') }}</strong>
                        <div
                            class="muted"
                            title="{{ $donationLabel }}"
                            style="display:block;white-space:nowrap;word-break:normal;overflow-wrap:normal;"
                        >
                            {{ $donationLabel }}
                        </div>
                        <div style="margin-top:8px;">
                            <button type="button" class="btn btn-secondary" data-modal-open="donation-modal-{{ $donation->id }}">Detail</button>
                        </div>
                    </div>
                </div>

                <div class="modal" id="donation-modal-{{ $donation->id }}" aria-hidden="true">
                    <div class="modal-backdrop" data-modal-close="donation-modal-{{ $donation->id }}"></div>
                    <div class="modal-dialog">
                        <div class="modal-header">
                            <strong>Detail Donasi</strong>
                            <button type="button" class="btn btn-secondary" data-modal-close="donation-modal-{{ $donation->id }}">Tutup</button>
                        </div>
                        <div class="modal-body">
                            <div class="detail-section">
                                <div><strong>Nominal:</strong> Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>
                                <div><strong>Status Pembayaran:</strong> {{ strtoupper($donation->payment_status) }}</div>
                                <div><strong>Status Verifikasi:</strong> {{ strtoupper($donation->verification_status) }}</div>
                                @if($donation->receipt_number)
                                    <div><strong>No. Kwitansi:</strong> {{ $donation->receipt_number }}</div>
                                @endif
                                @if($donation->paid_at)
                                    <div><strong>Tanggal Bayar:</strong> {{ $donation->paid_at->format('d-m-Y H:i') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="muted">Belum ada data donasi.</p>
            @endforelse
        </div>
        <div class="history-pagination">
            {{ $donations->appends(['per_page' => $perPage, 'reg_page' => request('reg_page', 1)])->links() }}
        </div>
    </div>
</div>
@endsection
