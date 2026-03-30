<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-brand">
            <h4>Portal Vihara</h4>
            <p>Ruang informasi kegiatan dan layanan umat.</p>
        </div>
        <div class="footer-links">
            <a href="{{ route('guest.home') }}">Beranda</a>
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Daftar</a>
            <a href="{{ route('guest.home') }}">Guest</a>
        </div>
    </div>
    <div class="footer-bottom">&copy; {{ now()->year }} Portal Vihara. Semua hak dilindungi.</div>
</footer>
