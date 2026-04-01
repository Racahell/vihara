import './bootstrap';
import Chart from 'chart.js/auto';
import Cropper from 'cropperjs';
import 'cropperjs/dist/cropper.css';
import { Html5Qrcode } from 'html5-qrcode';
import QRCode from 'qrcode';

window.Cropper = Cropper;

const parseQrPayload = (raw) => {
    if (!raw) return '';
    if (raw.startsWith('reg:')) return raw.replace('reg:', '').trim();
    return raw.trim();
};

const initDashboardCharts = () => {
    const charts = new Map();
    const palette = ['#f7b8d2', '#aee3bd', '#ffdca8', '#cde4ff', '#d9c5f3', '#f9a8d4', '#86efac'];

    const buildChart = (el, type) => {
        const labels = JSON.parse(el.dataset.labels || '[]');
        const values = JSON.parse(el.dataset.values || '[]');
        if (!labels.length || !values.length) return;

        const chartKey = el.dataset.chart || '';
        const unit = el.dataset.unit || (chartKey === 'donation-monthly' ? 'currency' : 'count');
        const datasetLabel = el.dataset.datasetLabel || (chartKey === 'donation-monthly' ? 'Donasi Bulanan (Rp)' : 'Total');
        const isMonthly = chartKey === 'donation-monthly';
        const configType = type || (isMonthly ? 'line' : 'doughnut');

        if (charts.has(el)) {
            charts.get(el).destroy();
            charts.delete(el);
        }

        const dataset = isMonthly
            ? {
                label: datasetLabel,
                data: values,
                borderColor: '#f08bb2',
                backgroundColor: palette,
                tension: 0.35,
                fill: configType === 'line',
            }
            : {
                label: datasetLabel,
                data: values,
                backgroundColor: palette,
                borderColor: '#ffffff',
                borderWidth: 1,
            };

        const cartesianTypes = ['line', 'bar'];
        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                },
            },
        };

        if (cartesianTypes.includes(configType)) {
            options.scales = {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (v) => {
                            const val = Number(v).toLocaleString('id-ID');
                            return unit === 'currency' ? `Rp ${val}` : val;
                        },
                    },
                },
            };
        }

        const instance = new Chart(el, {
            type: configType,
            data: {
                labels,
                datasets: [dataset],
            },
            options,
        });

        charts.set(el, instance);
    };

    document.querySelectorAll('[data-chart]').forEach((el) => {
        const selector = document.querySelector(`[data-chart-type][data-chart-target="${el.dataset.chart}"]`);
        const initialType = selector?.value || '';
        buildChart(el, initialType);
    });

    document.querySelectorAll('[data-chart-type]').forEach((selector) => {
        selector.addEventListener('change', () => {
            const chartName = selector.dataset.chartTarget;
            if (!chartName) return;
            const target = document.querySelector(`[data-chart="${chartName}"]`);
            if (!target) return;
            buildChart(target, selector.value);
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

const initTabs = () => {
    document.querySelectorAll('[data-tabs-wrapper]').forEach((wrapper) => {
        const buttons = wrapper.querySelectorAll('[data-tab-btn]');
        const root = wrapper.closest('.card') || wrapper.parentElement || document;
        const panels = root.querySelectorAll('[data-tab-panel]');
        if (!buttons.length || !panels.length) return;

        const showTab = (tabName) => {
            buttons.forEach((btn) => {
                btn.classList.toggle('active', btn.dataset.tabBtn === tabName);
            });
            panels.forEach((panel) => {
                panel.classList.toggle('is-active', panel.dataset.tabPanel === tabName);
            });
        };

        buttons.forEach((btn) => {
            btn.addEventListener('click', () => showTab(btn.dataset.tabBtn));
        });

        const activeBtn = wrapper.querySelector('[data-tab-btn].active');
        showTab(activeBtn?.dataset.tabBtn || buttons[0]?.dataset.tabBtn || '');
    });
};

const initAccessMatrix = () => {
    const wrapper = document.querySelector('[data-access-matrix]');
    if (!wrapper) return;

    const syncRow = (rowKey) => {
        const rowItems = wrapper.querySelectorAll(`[data-access-item][data-access-row="${rowKey}"]`);
        const rowToggle = wrapper.querySelector(`[data-access-row-toggle="${rowKey}"]`);
        if (!rowToggle || !rowItems.length) return;

        const checkedCount = Array.from(rowItems).filter((item) => item.checked).length;
        rowToggle.checked = checkedCount > 0 && checkedCount === rowItems.length;
        rowToggle.indeterminate = checkedCount > 0 && checkedCount < rowItems.length;
    };

    wrapper.querySelectorAll('[data-access-row-toggle]').forEach((toggle) => {
        const rowKey = toggle.dataset.accessRowToggle;
        if (!rowKey) return;

        toggle.addEventListener('change', () => {
            wrapper.querySelectorAll(`[data-access-item][data-access-row="${rowKey}"]`).forEach((item) => {
                if (item.disabled) return;
                item.checked = toggle.checked;
            });
            syncRow(rowKey);
        });

        syncRow(rowKey);
    });

    wrapper.querySelectorAll('[data-access-item]').forEach((item) => {
        const rowKey = item.dataset.accessRow;
        if (!rowKey) return;
        item.addEventListener('change', () => syncRow(rowKey));
    });
};

const initSingleSubmitForms = () => {
    document.querySelectorAll('form[data-prevent-double-submit]').forEach((form) => {
        form.addEventListener('submit', () => {
            const submitButton = form.querySelector('[data-submit-once], button[type="submit"], input[type="submit"]');
            if (!submitButton) return;

            submitButton.setAttribute('disabled', 'disabled');
            if (submitButton.tagName === 'BUTTON') {
                submitButton.textContent = 'Memproses...';
            }
        });
    });
};

const initProfileCrop = () => {
    document.querySelectorAll('[data-profile-crop]').forEach((form) => {
        const uploadInput = form.querySelector('[data-profile-photo-input]');
        const croppedInput = form.querySelector('[data-profile-cropped]');
        const preview = form.querySelector('[data-profile-preview]');
        const previewPlaceholder = form.querySelector('[data-profile-preview-placeholder]');
        const modal = document.querySelector('[data-profile-photo-modal]');
        const cropArea = modal?.querySelector('[data-profile-crop-area]');
        const cameraArea = modal?.querySelector('[data-profile-camera-area]');
        const cropImage = modal?.querySelector('[data-profile-crop-image]');
        const cameraVideo = modal?.querySelector('[data-profile-camera-video]');
        const openFileButtons = document.querySelectorAll('[data-profile-open-file]');
        const openCameraButtons = document.querySelectorAll('[data-profile-open-camera]');
        const captureButton = modal?.querySelector('[data-profile-camera-capture]');
        const closeButtons = modal?.querySelectorAll('[data-profile-modal-close]');
        const applyBtn = modal?.querySelector('[data-crop-apply]');
        const cancelBtn = modal?.querySelector('[data-crop-cancel]');

        if (!uploadInput || !croppedInput || !preview || !modal || !cropArea || !cameraArea || !cropImage || !cameraVideo || !captureButton || !applyBtn || !cancelBtn) {
            return;
        }

        let cropper = null;
        let stream = null;
        const showProfilePhotoError = (message) => {
            window.alert(message);
        };

        const openModal = () => {
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        };

        const closeModal = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            stopCamera();
        };

        const destroyCropper = () => {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            cropImage.src = '';
            cropArea.hidden = true;
        };

        const stopCamera = () => {
            if (!stream) return;
            stream.getTracks().forEach((track) => track.stop());
            stream = null;
            cameraVideo.srcObject = null;
            cameraArea.hidden = true;
        };

        const openCropperFromDataUrl = (dataUrl) => {
            if (!dataUrl) return;
            stopCamera();
            destroyCropper();
            cropImage.src = dataUrl;
            cropArea.hidden = false;
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                dragMode: 'move',
                background: false,
                zoomable: true,
                scalable: true,
                movable: true,
            });
        };

        const openCropperFromFile = (file) => {
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = () => openCropperFromDataUrl(String(reader.result || ''));
            reader.readAsDataURL(file);
        };

        const openFilePicker = (useCameraCapture = false) => {
            if (useCameraCapture) {
                uploadInput.setAttribute('capture', 'user');
                uploadInput.setAttribute('accept', 'image/*');
            } else {
                uploadInput.removeAttribute('capture');
                uploadInput.setAttribute('accept', 'image/png,image/jpeg,image/webp,image/*');
            }
            // Reset value so selecting the same file still triggers `change`.
            uploadInput.value = '';
            uploadInput.click();
        };

        const onFileChosen = (event) => {
            const file = event.target.files?.[0];
            if (!file) return;
            openModal();
            openCropperFromFile(file);
        };

        uploadInput.addEventListener('change', onFileChosen);

        cancelBtn.addEventListener('click', () => {
            uploadInput.value = '';
            croppedInput.value = '';
            destroyCropper();
            closeModal();
        });

        applyBtn.addEventListener('click', () => {
            if (!cropper) return;
            const canvas = cropper.getCroppedCanvas({
                width: 512,
                height: 512,
                imageSmoothingQuality: 'high',
            });
            const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
            croppedInput.value = dataUrl;
            preview.src = dataUrl;
            preview.style.display = 'block';
            if (previewPlaceholder) {
                previewPlaceholder.style.display = 'none';
            }
            destroyCropper();
            closeModal();
        });

        openFileButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                openModal();
                openFilePicker(false);
            });
        });

        const startCamera = async () => {
            openModal();
            destroyCropper();
            if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
                openFilePicker(true);
                return;
            }
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' },
                    audio: false,
                });
                cameraVideo.srcObject = stream;
                cameraArea.hidden = false;
            } catch (error) {
                cameraArea.hidden = true;
                openFilePicker(true);
                showProfilePhotoError('Kamera tidak dapat dibuka. Pastikan izin kamera aktif, lalu coba lagi.');
            }
        };

        openCameraButtons.forEach((btn) => {
            btn.addEventListener('click', startCamera);
        });

        captureButton.addEventListener('click', () => {
            if (!stream) return;
            const width = cameraVideo.videoWidth || 720;
            const height = cameraVideo.videoHeight || 720;
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            if (!ctx) return;
            ctx.drawImage(cameraVideo, 0, 0, width, height);
            openCropperFromDataUrl(canvas.toDataURL('image/jpeg', 0.95));
        });

        closeButtons?.forEach((btn) => {
            btn.addEventListener('click', () => {
                destroyCropper();
                closeModal();
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                destroyCropper();
                closeModal();
            }
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initDashboardCharts();
    initQrRender();
    initQrScanner();
    initModalHandlers();
    initTabs();
    initAccessMatrix();
    initSingleSubmitForms();
    initProfileCrop();
});
