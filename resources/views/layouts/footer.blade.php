@php
    $siteName = $websiteSettings['website_name'] ?? 'Portal Vihara';
    $companyDesc = $websiteSettings['company_description'] ?? 'Ruang informasi kegiatan dan layanan umat.';
    $contactPhone = $websiteSettings['contact_phone'] ?? null;
    $contactEmail = $websiteSettings['contact_email'] ?? null;
@endphp
<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <h4>{{ $siteName }}</h4>
            <p>{{ $companyDesc }}</p>
            @if($contactPhone || $contactEmail)
                <p class="muted" style="margin-top:8px;">
                    {{ $contactPhone ? 'Tel: ' . $contactPhone : '' }}
                    {{ $contactPhone && $contactEmail ? ' | ' : '' }}
                    {{ $contactEmail ? 'Email: ' . $contactEmail : '' }}
                </p>
            @endif
        </div>
        <div class="footer-links">
            <a href="{{ route('guest.home') }}">Beranda</a>
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Daftar</a>
            <a href="{{ route('guest.home') }}">Guest</a>
        </div>
    </div>
    <div class="footer-bottom">&copy; {{ now()->year }} {{ $siteName }}. Semua hak dilindungi.</div>
</footer>
