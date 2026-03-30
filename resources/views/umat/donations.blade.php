@extends('layouts.app')
@section('title', 'Donasi')
@section('content')
<div class="grid-2">
    @if($canCreateDonation)
        <div class="card">
            <h3>Buat Donasi</h3>
            <form action="{{ route('umat.donations.store') }}" method="POST" class="form-grid">
                @csrf
                <div class="grid-2">
                    <div>
                        <label for="donation-category">Kategori Donasi</label>
                        <select id="donation-category" name="donation_category_id">
                            <option value="">Kategori donasi</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="donation-activity">Terkait Kegiatan (Opsional)</label>
                        <select id="donation-activity" name="activity_id">
                            <option value="">Tidak terkait kegiatan</option>
                            @foreach($activities as $activity)
                                <option value="{{ $activity->id }}">{{ $activity->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="donation-amount">Nominal (Rp)</label>
                        <input id="donation-amount" type="number" name="amount" min="1000" placeholder="Contoh: 100000" required>
                    </div>
                    <div>
                        <label for="donation-channel">Metode Transfer</label>
                        <select id="donation-channel" name="payment_channel" required>
                            <option value="bank_transfer">Transfer Rekening</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="donor-type">Atas Nama</label>
                        <select id="donor-type" name="donor_type" required>
                            <option value="named">Nama Saya</option>
                            <option value="anonymous">Anonim</option>
                        </select>
                    </div>
                    <div>
                        <label for="donor-name">Nama Donatur (Opsional)</label>
                        <input id="donor-name" type="text" name="donor_name" value="{{ auth()->user()->name ?? '' }}" placeholder="Isi jika ingin nama lain">
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label for="donor-email">Email</label>
                        <input id="donor-email" type="email" name="donor_email" value="{{ auth()->user()->email ?? '' }}">
                    </div>
                    <div>
                        <label for="donor-phone">No HP</label>
                        <input id="donor-phone" type="text" name="donor_phone" value="{{ auth()->user()->phone ?? '' }}">
                    </div>
                </div>

                <div class="muted">Setelah instruksi transfer muncul, bukti transfer wajib diunggah.</div>

                <div>
                    <label for="donation-note">Catatan</label>
                    <input id="donation-note" type="text" name="note" placeholder="Catatan (opsional)">
                </div>
                <button type="submit">Buat Donasi &amp; Lihat Instruksi Transfer</button>
            </form>
        </div>
    @else
        <div class="card">
            <h3>Monitoring Donasi</h3>
            <p class="muted">Akun Owner/Ketua bersifat view-only. Pembuatan donasi dinonaktifkan.</p>
            @forelse($monitorDonations as $donation)
                <div style="padding:10px;border:1px solid #edf2f4;border-radius:10px;margin-bottom:8px;">
                    <strong>{{ $donation->donor_name ?? '-' }}</strong>
                    <div>Rp {{ number_format($donation->amount, 0, ',', '.') }}</div>
                    <div class="muted">{{ strtoupper($donation->payment_status) }} / {{ strtoupper($donation->verification_status) }}</div>
                </div>
            @empty
                <p class="muted">Belum ada data donasi.</p>
            @endforelse
        </div>
    @endif
    <div class="card">
        <h3>{{ $isOwnerReadOnly ? 'Ringkasan Donasi Anda' : 'Status Donasi Saya' }}</h3>
        @foreach($myDonations as $donation)
            <div style="padding:10px;border:1px solid #edf2f4;border-radius:10px;margin-bottom:8px;">
                <strong>Rp {{ number_format($donation->amount, 0, ',', '.') }}</strong>
                <div class="muted">{{ strtoupper($donation->payment_status) }} / {{ strtoupper($donation->verification_status) }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
