@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="card page-head">
    <h2>Profil Saya</h2>
    <p class="muted">Perbarui data akun, foto profil, dan kebutuhan reset password.</p>
</div>

<div style="margin-top:14px;">
    <div class="card">
        <h3>Data Profil</h3>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="form-grid" data-profile-crop>
            @csrf
            <div class="profile-photo-editor">
                <div class="profile-photo-preview-wrap">
                    @if(!empty($user->profile_photo_path))
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Foto profil" class="profile-photo-preview" data-profile-preview>
                    @else
                        <div class="profile-photo-placeholder" data-profile-preview-placeholder>{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        <img src="" alt="Foto profil" class="profile-photo-preview" data-profile-preview style="display:none;">
                    @endif
                </div>
                <div class="form-grid">
                    <div class="profile-photo-actions">
                        <button type="button" class="btn btn-secondary" data-profile-open-file>Pilih dari File</button>
                        <button type="button" class="btn btn-primary" data-profile-open-camera>Buka Kamera</button>
                    </div>
                    <small class="muted">Upload file biasa atau ambil foto langsung dari kamera. Setelah itu crop 1:1 dan simpan.</small>
                    <input id="profile_photo" type="file" name="profile_photo" accept="image/png,image/jpeg,image/webp" data-profile-photo-input hidden>
                    <input type="hidden" name="profile_photo_cropped" data-profile-cropped>
                </div>
            </div>

            <div>
                <label for="name">Nama lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div>
                <label for="phone">Nomor HP</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}">
            </div>
            <button class="btn btn-primary" type="submit">Simpan Profil</button>
        </form>
    </div>

    <div class="card" style="margin-top:14px;">
        <h3>Reset Password</h3>
        <p class="muted">Kirim link reset password ke email akun Anda atau generate link WhatsApp.</p>
        <div class="profile-reset-actions">
            <form action="{{ route('profile.reset-password.email') }}" method="POST" class="profile-reset-form">
                @csrf
                <button class="btn btn-primary" type="submit">Email</button>
            </form>
            <form action="{{ route('profile.reset-password.whatsapp') }}" method="POST" class="profile-reset-form">
                @csrf
                <button class="btn btn-secondary" type="submit">WhatsApp</button>
            </form>
        </div>
        @if($waResetUrl)
            <a class="btn btn-green" href="{{ $waResetUrl }}" target="_blank" rel="noopener" style="margin-top:10px;">Kirim ke WhatsApp Saya</a>
        @endif
        @error('reset_password')
            <div class="alert alert-error" style="margin-top:10px;">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="modal" id="profile-photo-modal" aria-hidden="true" data-profile-photo-modal>
    <button class="modal-backdrop" type="button" data-profile-modal-close></button>
    <div class="modal-dialog profile-photo-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="profile-photo-modal-title">
        <div class="modal-header">
            <strong id="profile-photo-modal-title">Foto Profil</strong>
            <button class="btn btn-secondary" type="button" data-profile-modal-close>Tutup</button>
        </div>
        <div class="modal-body profile-photo-modal-body">
            <div class="profile-modal-tools">
                <button type="button" class="btn btn-outline" data-profile-open-file>Ambil dari File</button>
                <button type="button" class="btn btn-outline" data-profile-open-camera>Ambil dari Kamera</button>
            </div>

            <div class="profile-camera-area" data-profile-camera-area hidden>
                <video data-profile-camera-video autoplay playsinline muted></video>
                <div class="profile-crop-actions">
                    <button class="btn btn-primary" type="button" data-profile-camera-capture>Ambil Foto</button>
                </div>
            </div>

            <div class="profile-crop-area" data-profile-crop-area hidden>
                <div class="profile-crop-canvas-wrap">
                    <img src="" alt="Crop foto profil" data-profile-crop-image>
                </div>
            </div>
            <div class="profile-crop-actions">
                <button class="btn btn-secondary" type="button" data-crop-cancel>Batal</button>
                <button class="btn btn-primary" type="button" data-crop-apply>Save Changes</button>
            </div>
        </div>
    </div>
</div>
@endsection
