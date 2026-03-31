@extends('layouts.app')
@section('title', 'Pengaturan Website')
@section('content')
<div class="card page-head">
    <h2>Pengaturan Umum Website</h2>
    <p class="muted">Atur identitas website, branding, kontak, dan konten halaman guest.</p>
</div>

<div class="card" style="margin-top:14px;" id="website-settings-card">
    <div class="tabs website-settings-tabs" data-tabs-wrapper>
        <button class="tab active" type="button" data-tab-btn="general">Umum</button>
        <button class="tab" type="button" data-tab-btn="branding">Branding</button>
        <button class="tab" type="button" data-tab-btn="contact">Kontak</button>
        <button class="tab" type="button" data-tab-btn="guest">Konten Guest</button>
    </div>

    <form action="{{ route('admin.website-settings.update') }}" method="POST" enctype="multipart/form-data" class="form-grid" style="margin-top:14px;">
        @csrf

        <section class="tab-panel is-active" data-tab-panel="general">
            <div class="grid-2">
                <div>
                    <label for="website_name">Nama Website</label>
                    <input id="website_name" type="text" name="website_name" value="{{ old('website_name', $settings['website_name']) }}" required>
                </div>
                <div>
                    <label for="website_url">Domain / URL</label>
                    <input id="website_url" type="url" name="website_url" placeholder="https://contoh.com" value="{{ old('website_url', $settings['website_url']) }}">
                </div>
            </div>
            <div class="grid-2">
                <div>
                    <label for="website_language">Bahasa</label>
                    <select id="website_language" name="website_language" required>
                        @php $language = old('website_language', $settings['website_language']); @endphp
                        <option value="id" {{ $language === 'id' ? 'selected' : '' }}>Indonesia</option>
                        <option value="en" {{ $language === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>
                <div>
                    <label for="website_timezone">Zona Waktu</label>
                    @php $timezone = old('website_timezone', $settings['website_timezone']); @endphp
                    <select id="website_timezone" name="website_timezone" required>
                        <option value="Asia/Jakarta" {{ $timezone === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
                        <option value="Asia/Makassar" {{ $timezone === 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
                        <option value="Asia/Jayapura" {{ $timezone === 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
                        <option value="Asia/Bangkok" {{ $timezone === 'Asia/Bangkok' ? 'selected' : '' }}>Asia/Bangkok</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="tab-panel" data-tab-panel="branding">
            <div class="grid-2">
                <div class="card">
                    <label for="website_logo">Logo Website</label>
                    <input id="website_logo" type="file" name="website_logo" accept="image/png,image/jpeg,image/webp,image/svg+xml">
                    <input id="website_logo_cropped" type="hidden" name="website_logo_cropped" value="">
                    <p class="muted" style="margin-top:8px;">Format: PNG/JPG/WEBP/SVG (maks 2MB).</p>
                    <div style="margin-top:10px;">
                        <img id="website_logo_preview" src="{{ !empty($settings['website_logo_path']) ? asset('storage/' . $settings['website_logo_path']) : '' }}" alt="Logo website" class="website-logo-preview" @if(empty($settings['website_logo_path'])) style="display:none;" @endif>
                    </div>
                </div>
                <div class="card">
                    <label for="website_favicon">Favicon</label>
                    <input id="website_favicon" type="file" name="website_favicon" accept="image/png,image/jpeg,image/jpg,image/x-icon,image/svg+xml">
                    <input id="website_favicon_cropped" type="hidden" name="website_favicon_cropped" value="">
                    <p class="muted" style="margin-top:8px;">Format: PNG/JPG/JPEG/ICO/SVG (maks 1MB).</p>
                    <div style="margin-top:10px;">
                        <img id="website_favicon_preview" src="{{ !empty($settings['website_favicon_path']) ? asset('storage/' . $settings['website_favicon_path']) : '' }}" alt="Favicon website" class="website-favicon-preview" @if(empty($settings['website_favicon_path'])) style="display:none;" @endif>
                    </div>
                </div>
            </div>
        </section>

        <section class="tab-panel" data-tab-panel="contact">
            <div class="grid-2">
                <div>
                    <label for="company_name">Nama Organisasi / Perusahaan</label>
                    <input id="company_name" type="text" name="company_name" value="{{ old('company_name', $settings['company_name']) }}">
                </div>
                <div>
                    <label for="manager_name">Nama Manager / Penanggung Jawab</label>
                    <input id="manager_name" type="text" name="manager_name" value="{{ old('manager_name', $settings['manager_name']) }}">
                </div>
            </div>
            <div class="grid-2">
                <div>
                    <label for="contact_phone">No. Telepon</label>
                    <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}">
                </div>
                <div>
                    <label for="contact_whatsapp">No. WhatsApp</label>
                    <input id="contact_whatsapp" type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp']) }}">
                </div>
            </div>
            <div>
                <label for="contact_email">Email Kontak</label>
                <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}">
            </div>
            <div>
                <label for="company_address">Alamat</label>
                <textarea id="company_address" name="company_address" rows="3">{{ old('company_address', $settings['company_address']) }}</textarea>
            </div>
            <div>
                <label for="company_description">Info Singkat</label>
                <textarea id="company_description" name="company_description" rows="3">{{ old('company_description', $settings['company_description']) }}</textarea>
            </div>
            <div class="grid-2">
                <div>
                    <label for="donation_bank_name">Bank Donasi</label>
                    <input id="donation_bank_name" type="text" name="donation_bank_name" value="{{ old('donation_bank_name', $settings['donation_bank_name']) }}" placeholder="Contoh: BCA">
                </div>
                <div>
                    <label for="donation_account_number">No. Rekening Donasi</label>
                    <input id="donation_account_number" type="text" name="donation_account_number" value="{{ old('donation_account_number', $settings['donation_account_number']) }}" placeholder="Contoh: 1234567890">
                </div>
            </div>
            <div>
                <label for="donation_account_holder">Atas Nama Rekening Donasi</label>
                <input id="donation_account_holder" type="text" name="donation_account_holder" value="{{ old('donation_account_holder', $settings['donation_account_holder']) }}" placeholder="Contoh: Yayasan Vihara">
            </div>
        </section>

        <section class="tab-panel" data-tab-panel="guest">
            <div>
                <label for="guest_hero_title">Judul Hero Guest</label>
                <input id="guest_hero_title" type="text" name="guest_hero_title" value="{{ old('guest_hero_title', $settings['guest_hero_title']) }}" required>
            </div>
            <div>
                <label for="guest_hero_subtitle">Subjudul Hero Guest</label>
                <textarea id="guest_hero_subtitle" name="guest_hero_subtitle" rows="3" required>{{ old('guest_hero_subtitle', $settings['guest_hero_subtitle']) }}</textarea>
            </div>
            <div>
                <label for="guest_about_title">Judul Tentang Vihara</label>
                <input id="guest_about_title" type="text" name="guest_about_title" value="{{ old('guest_about_title', $settings['guest_about_title']) }}" required>
            </div>
            <div>
                <label for="guest_about_description">Deskripsi Tentang Vihara</label>
                <textarea id="guest_about_description" name="guest_about_description" rows="5" required>{{ old('guest_about_description', $settings['guest_about_description']) }}</textarea>
            </div>
            <div>
                <label for="vihara_location_name">Nama Lokasi Vihara</label>
                <input id="vihara_location_name" type="text" name="vihara_location_name" value="{{ old('vihara_location_name', $settings['vihara_location_name']) }}" required>
            </div>
            <div>
                <label for="vihara_location_address">Alamat Vihara</label>
                <textarea id="vihara_location_address" name="vihara_location_address" rows="3" required>{{ old('vihara_location_address', $settings['vihara_location_address']) }}</textarea>
            </div>
            <div>
                <label for="vihara_map_url">Link Google Maps</label>
                <input id="vihara_map_url" type="url" name="vihara_map_url" value="{{ old('vihara_map_url', $settings['vihara_map_url']) }}" required>
            </div>
        </section>

        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
    </form>
</div>

<div class="modal" id="branding-crop-modal" aria-hidden="true">
    <div class="modal-backdrop" data-branding-crop-cancel></div>
    <div class="modal-dialog website-crop-modal-dialog">
        <div class="modal-header">
            <strong id="branding-crop-title">Crop Gambar</strong>
            <button type="button" class="btn btn-secondary" data-branding-crop-cancel>Tutup</button>
        </div>
        <div class="modal-body">
            <div class="website-crop-wrap">
                <img id="branding-crop-image" alt="Preview crop" style="max-width:100%;display:block;">
            </div>
            <div class="modal-footer-actions">
                <button type="button" class="btn btn-secondary" data-branding-crop-cancel>Batal</button>
                <button type="button" class="btn btn-primary" id="branding-crop-apply">Pakai Hasil Crop</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const card = document.getElementById('website-settings-card');
    if (!card) return;

    const buttons = card.querySelectorAll('[data-tab-btn]');
    const panels = card.querySelectorAll('[data-tab-panel]');
    if (!buttons.length || !panels.length) return;

    const showTab = function (tabName) {
        buttons.forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.tabBtn === tabName);
        });
        panels.forEach(function (panel) {
            panel.classList.toggle('is-active', panel.dataset.tabPanel === tabName);
        });
    };

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            showTab(btn.dataset.tabBtn);
        });
    });

    const activeButton = card.querySelector('[data-tab-btn].active');
    showTab(activeButton ? activeButton.dataset.tabBtn : 'general');

    const logoInput = document.getElementById('website_logo');
    const faviconInput = document.getElementById('website_favicon');
    const logoHidden = document.getElementById('website_logo_cropped');
    const faviconHidden = document.getElementById('website_favicon_cropped');
    const logoPreview = document.getElementById('website_logo_preview');
    const faviconPreview = document.getElementById('website_favicon_preview');
    const modal = document.getElementById('branding-crop-modal');
    const modalImage = document.getElementById('branding-crop-image');
    const modalTitle = document.getElementById('branding-crop-title');
    const applyBtn = document.getElementById('branding-crop-apply');
    const cancelButtons = document.querySelectorAll('[data-branding-crop-cancel]');

    if (!logoInput || !faviconInput || !logoHidden || !faviconHidden || !modal || !modalImage || !applyBtn || typeof window.Cropper === 'undefined') return;

    let cropper = null;
    let currentTarget = null;

    const openModal = function () {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    };

    const closeModal = function () {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        modalImage.src = '';
        currentTarget = null;
    };

    const openCropper = function (file, target) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function () {
            currentTarget = target;
            modalTitle.textContent = target === 'favicon' ? 'Crop Favicon' : 'Crop Logo Website';
            modalImage.src = String(reader.result || '');
            openModal();
            if (cropper) cropper.destroy();
            cropper = new window.Cropper(modalImage, {
                aspectRatio: target === 'favicon' ? 1 : NaN,
                viewMode: 1,
                autoCropArea: 1,
                dragMode: 'move',
                background: false,
                movable: true,
                scalable: true,
                zoomable: true,
            });
        };
        reader.readAsDataURL(file);
    };

    logoInput.addEventListener('change', function (event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;
        openCropper(file, 'logo');
    });

    faviconInput.addEventListener('change', function (event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;
        openCropper(file, 'favicon');
    });

    applyBtn.addEventListener('click', function () {
        if (!cropper || !currentTarget) return;
        const canvas = cropper.getCroppedCanvas({
            width: currentTarget === 'favicon' ? 512 : 1200,
            height: currentTarget === 'favicon' ? 512 : 600,
            imageSmoothingQuality: 'high',
        });
        const dataUrl = canvas.toDataURL(currentTarget === 'favicon' ? 'image/png' : 'image/jpeg', 0.92);

        if (currentTarget === 'logo') {
            logoHidden.value = dataUrl;
            logoInput.value = '';
            if (logoPreview) {
                logoPreview.src = dataUrl;
                logoPreview.style.display = 'block';
            }
        } else {
            faviconHidden.value = dataUrl;
            faviconInput.value = '';
            if (faviconPreview) {
                faviconPreview.src = dataUrl;
                faviconPreview.style.display = 'block';
            }
        }

        closeModal();
    });

    cancelButtons.forEach(function (btn) {
        btn.addEventListener('click', closeModal);
    });
});
</script>
@endsection
