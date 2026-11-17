(() => {
  const form = document.querySelector('[data-qr-scan-form]');
  const video = document.querySelector('[data-qr-video]');
  const start = document.querySelector('[data-qr-start]');
  const stop = document.querySelector('[data-qr-stop]');
  const status = document.querySelector('[data-qr-status]');
  const input = document.querySelector('[data-qr-input]');
  if (!form || !video || !start || !input) return;
  let stream = null;
  let active = false;
  let detector = null;
  const say = (message, tone = 'info') => {
    if (!status) return;
    status.textContent = message;
    status.className = `alert alert-${tone} mt-3 mb-0`;
    status.hidden = false;
  };
  const stopCamera = () => {
    active = false;
    if (stream) stream.getTracks().forEach((track) => track.stop());
    stream = null;
    video.srcObject = null;
    if (stop) stop.hidden = true;
    start.hidden = false;
  };
  const scanLoop = async () => {
    if (!active || !detector) return;
    try {
      const codes = await detector.detect(video);
      if (codes.length && codes[0].rawValue) {
        input.value = codes[0].rawValue.trim();
        say('QR terbaca. Mengirim payload ke server untuk verifikasi database…', 'success');
        stopCamera();
        form.requestSubmit();
        return;
      }
    } catch (_) { /* Frame kamera sesaat tidak terbaca; loop berlanjut. */ }
    if (active) window.setTimeout(scanLoop, 180);
  };
  start.addEventListener('click', async () => {
    if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia) {
      say('Kamera memerlukan HTTPS atau localhost dan izin kamera. Gunakan input nomor/payload QR manual.', 'warning');
      return;
    }
    if (!('BarcodeDetector' in window)) {
      say('Browser ini belum mendukung pemindai QR bawaan. Gunakan input manual di bawah.', 'warning');
      return;
    }
    try {
      detector = new BarcodeDetector({ formats: ['qr_code'] });
      stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
      video.srcObject = stream;
      await video.play();
      active = true;
      start.hidden = true;
      if (stop) stop.hidden = false;
      say('Kamera aktif. Arahkan QR invoice ke dalam bingkai.', 'success');
      scanLoop();
    } catch (error) {
      stopCamera();
      say(error?.name === 'NotAllowedError' ? 'Izin kamera ditolak. Izinkan kamera di browser atau gunakan input manual.' : 'Kamera tidak dapat diakses. Gunakan input manual.', 'danger');
    }
  });
  stop?.addEventListener('click', () => { stopCamera(); say('Kamera dihentikan.', 'secondary'); });
  window.addEventListener('pagehide', stopCamera);
})();
