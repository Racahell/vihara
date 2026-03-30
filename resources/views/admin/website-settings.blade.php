@extends('layouts.app')
@section('title', 'Pengaturan Website')
@section('content')
<div class="card page-head">
    <h2>Pengaturan Website</h2>
    <p class="muted">Edit konten halaman guest: judul utama dan bagian Tentang Vihara.</p>
</div>

<div class="card" style="margin-top:14px;">
    <form action="{{ route('admin.website-settings.update') }}" method="POST" class="form-grid">
        @csrf
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
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
    </form>
</div>
@endsection
