@extends('layouts.app')
@section('title', 'Verifikasi Donasi')
@section('content')
<style>
.proof-thumb-link { display: inline-block; }
.proof-thumb {
    width: 72px;
    height: 72px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    object-fit: cover;
    display: block;
    cursor: zoom-in;
    background: #fff;
}
.proof-viewer {
    position: fixed;
    inset: 0;
    z-index: 1200;
    display: none;
}
.proof-viewer.is-open { display: block; }
.proof-viewer-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.72);
}
.proof-viewer-dialog {
    position: relative;
    width: min(94vw, 1080px);
    max-height: 92vh;
    margin: 2vh auto;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    display: grid;
    grid-template-rows: auto 1fr;
}
.proof-viewer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    border-bottom: 1px solid #e2e8f0;
}
.proof-viewer-tools {
    display: inline-flex;
    gap: 8px;
    align-items: center;
}
.proof-viewer-canvas {
    position: relative;
    overflow: auto;
    background: #f8fafc;
    min-height: 320px;
    max-height: calc(92vh - 62px);
    display: grid;
    place-items: center;
    padding: 16px;
}
.proof-viewer-image {
    max-width: 100%;
    max-height: calc(92vh - 110px);
    transform-origin: center center;
    transition: transform 0.14s ease;
    user-select: none;
    -webkit-user-drag: none;
}
</style>
<div class="table-toolbar" style="margin:0 0 10px 0;">
    <h3 style="margin:0;">Daftar Verifikasi Donasi</h3>
    <form method="GET" class="table-length">
        <label for="donation-verification-per-page">Tampilkan</label>
        <select id="donation-verification-per-page" name="per_page" onchange="this.form.submit()">
            @foreach([10, 25, 50, 100] as $size)
                <option value="{{ $size }}" @selected((int) ($perPage ?? 10) === $size)>{{ $size }}</option>
            @endforeach
        </select>
    </form>
</div>
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
                    @if($donation->proof_is_image)
                        <a
                            class="proof-thumb-link"
                            href="{{ $donation->proof_preview_url }}"
                            data-proof-open
                            data-proof-src="{{ $donation->proof_preview_url }}"
                            data-proof-title="Bukti Transfer Donasi #{{ $donation->id }}">
                            <img src="{{ $donation->proof_preview_url }}" alt="Bukti transfer #{{ $donation->id }}" class="proof-thumb">
                        </a>
                    @else
                        <span class="muted">Format bukan gambar</span>
                    @endif
                @else
                    <span class="muted">Belum upload</span>
                @endif
            </td>
            <td>{{ strtoupper($donation->verification_status) }}</td>
            <td>
                <button type="button" class="btn btn-outline" data-modal-open="donation-modal-{{ $donation->id }}">Detail</button>
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
                    @if($donation->receipt_pdf_path)
                        <div style="margin-top:10px;">
                            <a class="btn btn-green" href="{{ route('admin.donation-receipts.download', $donation) }}">Unduh Kwitansi</a>
                        </div>
                    @endif
                </div>

                @if($donation->bank_transfer_proof_path)
                    <div class="detail-section">
                        @if($donation->proof_is_image)
                            <a
                                class="proof-thumb-link"
                                href="{{ $donation->proof_preview_url }}"
                                data-proof-open
                                data-proof-src="{{ $donation->proof_preview_url }}"
                                data-proof-title="Bukti Transfer Donasi #{{ $donation->id }}">
                                <img src="{{ $donation->proof_preview_url }}" alt="Bukti transfer #{{ $donation->id }}" class="proof-thumb" style="width:120px;height:120px;">
                            </a>
                        @else
                            <span class="muted">Format bukti bukan gambar</span>
                        @endif
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
<div class="proof-viewer" id="proof-viewer" aria-hidden="true">
    <button type="button" class="proof-viewer-backdrop" data-proof-close></button>
    <div class="proof-viewer-dialog" role="dialog" aria-modal="true" aria-labelledby="proof-viewer-title">
        <div class="proof-viewer-header">
            <strong id="proof-viewer-title">Bukti Transfer</strong>
            <div class="proof-viewer-tools">
                <button type="button" class="btn btn-outline" data-proof-zoom-out>Zoom -</button>
                <button type="button" class="btn btn-outline" data-proof-zoom-reset>Reset</button>
                <button type="button" class="btn btn-outline" data-proof-zoom-in>Zoom +</button>
                <button type="button" class="btn btn-secondary" data-proof-close>Tutup</button>
            </div>
        </div>
        <div class="proof-viewer-canvas">
            <img src="" alt="Preview bukti transfer" class="proof-viewer-image" data-proof-image>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewer = document.getElementById('proof-viewer');
    const image = viewer ? viewer.querySelector('[data-proof-image]') : null;
    const title = viewer ? viewer.querySelector('#proof-viewer-title') : null;
    if (!viewer || !image || !title) return;

    const openButtons = document.querySelectorAll('[data-proof-open]');
    const closeButtons = viewer.querySelectorAll('[data-proof-close]');
    const zoomInBtn = viewer.querySelector('[data-proof-zoom-in]');
    const zoomOutBtn = viewer.querySelector('[data-proof-zoom-out]');
    const zoomResetBtn = viewer.querySelector('[data-proof-zoom-reset]');
    const canvas = viewer.querySelector('.proof-viewer-canvas');

    let zoom = 1;
    const minZoom = 0.3;
    const maxZoom = 5;

    const applyZoom = function () {
        image.style.transform = 'scale(' + zoom + ')';
    };

    const openViewer = function (src, label) {
        zoom = 1;
        image.src = src;
        applyZoom();
        title.textContent = label || 'Bukti Transfer';
        viewer.classList.add('is-open');
        viewer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    };

    const closeViewer = function () {
        viewer.classList.remove('is-open');
        viewer.setAttribute('aria-hidden', 'true');
        image.src = '';
        document.body.style.overflow = '';
    };

    openButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const src = button.getAttribute('data-proof-src') || '';
            if (!src) return;
            openViewer(src, button.getAttribute('data-proof-title') || 'Bukti Transfer');
        });
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeViewer);
    });

    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function () {
            zoom = Math.min(maxZoom, +(zoom + 0.2).toFixed(2));
            applyZoom();
        });
    }

    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function () {
            zoom = Math.max(minZoom, +(zoom - 0.2).toFixed(2));
            applyZoom();
        });
    }

    if (zoomResetBtn) {
        zoomResetBtn.addEventListener('click', function () {
            zoom = 1;
            applyZoom();
        });
    }

    if (canvas) {
        canvas.addEventListener('wheel', function (event) {
            if (!viewer.classList.contains('is-open')) return;
            event.preventDefault();
            const direction = event.deltaY < 0 ? 1 : -1;
            zoom = Math.max(minZoom, Math.min(maxZoom, +(zoom + (0.12 * direction)).toFixed(2)));
            applyZoom();
        }, { passive: false });
    }

    document.addEventListener('keydown', function (event) {
        if (viewer.classList.contains('is-open') && event.key === 'Escape') {
            closeViewer();
        }
    });
});
</script>
@endsection
