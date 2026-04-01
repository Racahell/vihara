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
            <a href="#beranda" class="guest-brand" aria-label="{{ $websiteSettings['website_name'] }}">
                @if(!empty($websiteSettings['website_logo_path']))
                    <img src="{{ asset('storage/' . $websiteSettings['website_logo_path']) }}" alt="Logo {{ $websiteSettings['website_name'] }}" class="guest-logo-image">
                @else
                    <span class="guest-brand-logo" aria-hidden="true">
                        <svg viewBox="0 0 64 64" role="img">
                            <defs>
                                <linearGradient id="viharaLogoGrad" x1="0" x2="1" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#14b8a6"/>
                                    <stop offset="100%" stop-color="#0f766e"/>
                                </linearGradient>
                            </defs>
                            <rect x="2" y="2" width="60" height="60" rx="18" fill="url(#viharaLogoGrad)"/>
                            <path d="M13 41c7-8 14-12 23-12s17 4 25 12H13z" fill="#e6fffa"/>
                            <path d="M32 14c3 4 5 8 5 12a5 5 0 1 1-10 0c0-4 2-8 5-12z" fill="#ffffff"/>
                        </svg>
                    </span>
                @endif
                <span class="guest-brand-copy">
                    <strong>{{ $websiteSettings['website_name'] }}</strong>
                    <span>Perahu &amp; Teratai</span>
                </span>
            </a>
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
                @php
                    $mapAddress = (string) ($websiteSettings['vihara_location_address'] ?? '');
                    $mapUrl = trim((string) ($websiteSettings['vihara_map_url'] ?? ''));
                    if ($mapUrl !== '' && preg_match('/[?&](?:q|query)=([^&]+)/i', $mapUrl, $matches) === 1) {
                        $parsedAddress = urldecode($matches[1] ?? '');
                        if ($parsedAddress !== '') {
                            $mapAddress = $parsedAddress;
                        }
                    }
                    $mapEmbedUrl = 'https://www.google.com/maps?q=' . urlencode($mapAddress) . '&output=embed';
                @endphp
                <div class="guest-map-frame">
                    <iframe
                        title="Lokasi Vihara"
                        src="{{ $mapEmbedUrl }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div style="margin-top:10px;">
                    <a class="btn btn-secondary" href="{{ $mapUrl !== '' ? $mapUrl : $mapEmbedUrl }}" target="_blank" rel="noopener">Buka di Google Maps</a>
                </div>
            </div>
        </div>
    </section>

    <section id="donasi" class="guest-section guest-cta-wrap">
        <div class="guest-container">
            <div class="card guest-donation-card">
                <div>
                    <h2>Donasi Sebagai Guest</h2>
                    <p class="muted">Guest dapat berdonasi tanpa login. Pilih atas nama tertentu atau anonim, lalu transfer sesuai nominal ke rekening yang disediakan.</p>
                </div>
                <form action="{{ route('guest.donations.store') }}" method="POST" class="form-grid" data-prevent-double-submit>
                    @csrf
                    <input type="hidden" name="submission_token" value="{{ $guestDonationSubmissionToken ?? '' }}">
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
                            <label>Metode Pembayaran</label>
                            <input type="text" value="Transfer Rekening (Manual)" readonly>
                            <input type="hidden" name="payment_channel" value="bank_transfer">
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

                    <button class="btn btn-green" type="submit" data-submit-once>Buat Donasi Guest</button>
                </form>
            </div>
        </div>
    </section>

    @include('layouts.footer', ['websiteSettings' => $websiteSettings])
</div>
</body>
</html>
