import './bootstrap';
<<<<<<< HEAD
import Chart from 'chart.js/auto';
import { Html5Qrcode } from 'html5-qrcode';
import QRCode from 'qrcode';

const parseQrPayload = (raw) => {
    if (!raw) return '';
    if (raw.startsWith('reg:')) return raw.replace('reg:', '').trim();
    return raw.trim();
};

const initDashboardCharts = () => {
    document.querySelectorAll('[data-chart="donation-monthly"]').forEach((el) => {
        const labels = JSON.parse(el.dataset.labels || '[]');
        const values = JSON.parse(el.dataset.values || '[]');

        if (!labels.length || !values.length) return;

        new Chart(el, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Donasi Bulanan (Rp)',
                    data: values,
                    borderColor: '#f08bb2',
                    backgroundColor: 'rgba(240, 139, 178, 0.18)',
                    tension: 0.35,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        ticks: {
                            callback: (v) => `Rp ${Number(v).toLocaleString('id-ID')}`,
                        },
                    },
                },
            },
        });
    });

    document.querySelectorAll('[data-chart="category-breakdown"]').forEach((el) => {
        const labels = JSON.parse(el.dataset.labels || '[]');
        const values = JSON.parse(el.dataset.values || '[]');
        if (!labels.length || !values.length) return;

        new Chart(el, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#f7b8d2', '#aee3bd', '#ffdca8', '#cde4ff', '#d9c5f3'],
                }],
            },
            options: { responsive: true, maintainAspectRatio: false },
        });
    });
};

const initQrRender = () => {
    document.querySelectorAll('[data-qr-payload]').forEach((el) => {
        const payload = el.dataset.qrPayload;
        if (!payload) return;

        QRCode.toDataURL(payload, { width: 180, margin: 1 })
            .then((url) => {
                el.setAttribute('src', url);
            })
            .catch(() => {
                el.replaceWith(document.createTextNode('QR gagal dibuat'));
            });
    });
};

const initQrScanner = () => {
    const readerEl = document.getElementById('qr-reader');
    const inputEl = document.getElementById('registration_code');
    const formEl = document.getElementById('checkin-code-form');
    const resultEl = document.getElementById('scan-result');

    if (!readerEl || !inputEl || !formEl) return;

    const qr = new Html5Qrcode('qr-reader');
    let active = false;
    let submitting = false;
    let lastScannedCode = '';

    const onScanSuccess = (decodedText) => {
        const code = parseQrPayload(decodedText);
        if (!code || submitting) return;
        if (code === lastScannedCode) return;

        lastScannedCode = code;
        inputEl.value = code;

        if (resultEl) {
            resultEl.textContent = `QR terbaca: ${code}. Memproses check-in...`;
        }

        submitting = true;
        if (active) {
            qr.stop().catch(() => {});
            active = false;
        }

        if (typeof formEl.requestSubmit === 'function') {
            formEl.requestSubmit();
        } else {
            formEl.submit();
        }
    };

    const startBtn = document.getElementById('start-scan-btn');
    const stopBtn = document.getElementById('stop-scan-btn');

    const start = async () => {
        if (active) return;
        try {
            submitting = false;
            lastScannedCode = '';
            await qr.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 260, height: 260 } },
                onScanSuccess,
                () => {}
            );
            active = true;
            if (resultEl) resultEl.textContent = 'Scanner aktif. Arahkan kamera ke QR tiket.';
        } catch (e) {
            if (resultEl) resultEl.textContent = 'Gagal membuka kamera. Pastikan izin kamera aktif.';
        }
    };

    const stop = async () => {
        if (!active) return;
        await qr.stop();
        await qr.clear();
        active = false;
        if (resultEl) resultEl.textContent = 'Scanner dihentikan.';
    };

    startBtn?.addEventListener('click', start);
    stopBtn?.addEventListener('click', stop);
};

const initModalHandlers = () => {
    const openButtons = document.querySelectorAll('[data-modal-open]');
    const closeButtons = document.querySelectorAll('[data-modal-close]');

    const openModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    };

    const closeModal = (id) => {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    openButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            openModal(btn.dataset.modalOpen);
        });
    });

    closeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            closeModal(btn.dataset.modalClose);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('.modal.is-open').forEach((modal) => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initDashboardCharts();
    initQrRender();
    initQrScanner();
    initModalHandlers();
});
=======
>>>>>>> e2927c017d800ba2c0919a3f2a14f7de18623268
