<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $websiteSettings['website_name'] }} - Guest</title>
    @if(!empty($websiteSettings['website_favicon_path']))
        <link rel="icon" href="{{ asset('storage/' . $websiteSettings['website_favicon_path']) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="guest-page">
    <header class="guest-header">
        <div class="guest-container guest-nav">
            <div class="guest-brand-wrap">
                @if(!empty($websiteSettings['website_logo_path']))
                    <img src="{{ asset('storage/' . $websiteSettings['website_logo_path']) }}" alt="Logo {{ $websiteSettings['website_name'] }}" class="guest-logo-image">
                @else
                    <div class="auth-logo-mark" aria-hidden="true">
                        <svg viewBox="0 0 64 64" role="img">
                            <circle cx="32" cy="32" r="30" fill="#ecfdf5" stroke="#99f6e4" stroke-width="2"/>
                            <path d="M13 39c6 0 10-3 19-3s13 3 19 3" fill="none" stroke="#0f766e" stroke-width="3" stroke-linecap="round"/>
                            <path d="M24 30l8-12 8 12" fill="none" stroke="#ec4899" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M29 30h6" fill="none" stroke="#ec4899" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </div>
                @endif
                <div class="guest-brand">
                    <strong>{{ $websiteSettings['website_name'] }}</strong>
                    <span>Perahu &amp; Teratai</span>
                </div>
            </div>
            <nav class="guest-links">
                <a href="#beranda">Beranda</a>
                <a href="#tentang">Tentang Vihara</a>
                <a href="#donasi">Donasi</a>
            </nav>
            <div class="guest-auth-actions">
                <a class="btn btn-secondary" href="{{ route('login') }}">Login</a>
                <a class="btn btn-primary" href="{{ route('register') }}">Daftar</a>
            </div>
        </div>
    </header>

    <section id="beranda" class="guest-hero">
        <div class="guest-container guest-hero-grid">
            <div>
                <h1>{{ $websiteSettings['guest_hero_title'] }}</h1>
                <p class="muted">{{ $websiteSettings['guest_hero_subtitle'] }}</p>
                @if (session('status'))
                    <div class="alert alert-success" style="margin-top:10px;">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-error" style="margin-top:10px;">{{ $errors->first() }}</div>
                @endif
                <div class="auth-action-row" style="margin-top:14px;">
                    <a class="btn btn-green" href="{{ route('register') }}">Daftar Sekarang</a>
                    <a class="btn btn-secondary" href="{{ route('login') }}">Login</a>
                </div>
                <div class="guest-stats">
                    <div class="guest-stat"><strong>{{ number_format($stats['activities']) }}+</strong><span>Kegiatan Aktif</span></div>
                    <div class="guest-stat"><strong>{{ number_format($stats['participants']) }}+</strong><span>Peserta Terdaftar</span></div>
                    <div class="guest-stat"><strong>{{ number_format($stats['donations']) }}+</strong><span>Donasi Tercatat</span></div>
                </div>
            </div>
            <div class="guest-symbol-card">
                <h3>Makna Simbol</h3>
                <p><strong>Perahu</strong> melambangkan perjalanan kebajikan bersama menuju kebijaksanaan.</p>
                <p><strong>Teratai</strong> melambangkan kemurnian hati, tumbuh di tengah kehidupan namun tetap bersih.</p>
                <div class="guest-symbols">
                    <span>PERAHU</span>
                    <span>TERATAI</span>
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="guest-section">
        <div class="guest-container">
            <h2>Tentang Vihara</h2>
            <div class="grid-2" style="margin-top:12px;">
                <article class="card">
                    <h3>{{ $aboutVihara['title'] }}</h3>
                    <p class="muted">{{ $aboutVihara['description'] }}</p>
                </article>
                <article class="card">
                    <h3>Kegiatan Terdekat</h3>
                    @forelse($latestActivities as $activity)
                        <div style="padding:8px 0;border-bottom:1px dashed #e8eef0;">
                            <strong>{{ $activity->title }}</strong>
                            <div class="muted">{{ $activity->start_at?->format('d M Y H:i') }} | {{ $activity->location ?: 'Vihara' }}</div>
                        </div>
                    @empty
                        <p class="muted">Belum ada kegiatan aktif.</p>
                    @endforelse
                </article>
            </div>

            <div class="card" style="margin-top:12px;">
                <h3>Lokasi Vihara</h3>
                <p><strong>{{ $websiteSettings['vihara_location_name'] }}</strong></p>
                <p class="muted">{{ $websiteSettings['vihara_location_address'] }}</p>
                <div class="guest-map-frame">
                    <iframe
                        title="Lokasi Vihara"
                        src="{{ 'https://www.google.com/maps?q=' . urlencode($websiteSettings['vihara_location_address']) . '&output=embed' }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div style="margin-top:10px;">
                    <a class="btn btn-secondary" href="{{ $websiteSettings['vihara_map_url'] }}" target="_blank" rel="noopener">Buka di Google Maps</a>
                </div>
            </div>
        </div>
    </section>

    <section id="donasi" class="guest-section guest-cta-wrap">
        <div class="guest-container">
            <div class="card guest-donation-card">
                <div>
                    <h2>Donasi Sebagai Guest</h2>
                    <p class="muted">Guest dapat berdonasi tanpa login. Pilih atas nama tertentu atau anonim, lalu transfer sesuai nominal ke rekening atau QR yang disediakan.</p>
                </div>
                <form action="{{ route('guest.donations.store') }}" method="POST" class="form-grid">
                    @csrf
                    <div class="grid-2">
                        <div>
                            <label for="guest-category">Kategori Donasi</label>
                            <select id="guest-category" name="donation_category_id">
                                <option value="">Umum</option>
                                @foreach($donationCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="guest-amount">Nominal (Rp)</label>
                            <input id="guest-amount" type="number" name="amount" min="1000" placeholder="Contoh: 100000" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div>
                            <label for="guest-channel">Metode Transfer</label>
                            <select id="guest-channel" name="payment_channel" required>
                                <option value="bank_transfer">Transfer Rekening</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div>
                            <label for="guest-donor-type">Atas Nama</label>
                            <select id="guest-donor-type" name="donor_type" required>
                                <option value="named">Isi Nama Donatur</option>
                                <option value="anonymous">Anonim</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div>
                            <label for="guest-donor-name">Nama Donatur</label>
                            <input id="guest-donor-name" type="text" name="donor_name" placeholder="Nama sesuai transfer (opsional jika anonim)">
                        </div>
                        <div>
                            <label for="guest-donor-email">Email (Opsional)</label>
                            <input id="guest-donor-email" type="email" name="donor_email" placeholder="email@contoh.com">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div>
                            <label for="guest-donor-phone">No HP (Opsional)</label>
                            <input id="guest-donor-phone" type="text" name="donor_phone" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label>Upload Bukti</label>
                            <div class="muted">Setelah instruksi transfer muncul, bukti transfer wajib diunggah pada langkah berikutnya.</div>
                        </div>
                    </div>

                    <div>
                        <label for="guest-note">Catatan</label>
                        <input id="guest-note" type="text" name="note" placeholder="Contoh: Donasi untuk kegiatan sosial">
                    </div>

                    <button class="btn btn-green" type="submit">Buat Donasi Guest</button>
                </form>
            </div>
        </div>
    </section>

    @include('layouts.footer', ['websiteSettings' => $websiteSettings])
</div>
</body>
</html>
