@extends('layouts.app')
@section('title', 'Pengaturan Website')
@section('content')
<div class="card page-head">
    <h2>Pengaturan Umum Website</h2>
    <p class="muted">Atur identitas website, branding, kontak, dan konten halaman guest.</p>
</div>

<div class="card" style="margin-top:14px;">
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
                    <p class="muted" style="margin-top:8px;">Format: PNG/JPG/WEBP/SVG (maks 2MB).</p>
                    @if(!empty($settings['website_logo_path']))
                        <div style="margin-top:10px;">
                            <img src="{{ asset('storage/' . $settings['website_logo_path']) }}" alt="Logo website" class="website-logo-preview">
                        </div>
                    @endif
                </div>
                <div class="card">
                    <label for="website_favicon">Favicon</label>
                    <input id="website_favicon" type="file" name="website_favicon" accept="image/png,image/x-icon,image/svg+xml">
                    <p class="muted" style="margin-top:8px;">Format: PNG/ICO/SVG (maks 1MB).</p>
                    @if(!empty($settings['website_favicon_path']))
                        <div style="margin-top:10px;">
                            <img src="{{ asset('storage/' . $settings['website_favicon_path']) }}" alt="Favicon website" class="website-favicon-preview">
                        </div>
                    @endif
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
@endsection
