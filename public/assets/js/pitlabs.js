/*
 * PITLABS — interaksi frontend (Fase 2)
 * Hanya perilaku bersama: validasi form sisi klien, simulasi submit, dan state loading.
 * Validasi sisi server tetap wajib saat backend dibuat (Fase 5).
 */
(function () {
  'use strict';

  /* ---------- Validasi form (form[data-pit-validate]) ---------- */

  // Kolom yang harus sama dengan kolom lain (konfirmasi kata sandi): data-match="#id-kolom"
  function applyMatch(el) {
    var sel = el.getAttribute('data-match');
    if (!sel) return;
    var other = document.querySelector(sel);
    el.setCustomValidity(other && el.value !== '' && el.value !== other.value ? (el.getAttribute('data-msg-match') || 'Isian tidak sama.') : '');
  }

  function messageFor(el) {
    var v = el.validity;
    if (el.dataset.msg) return el.dataset.msg;
    if (v.customError) return el.validationMessage;
    if (v.valueMissing) return el.type === 'checkbox' ? 'Centang kotak ini untuk melanjutkan.' : 'Kolom ini wajib diisi.';
    if (v.typeMismatch) return el.type === 'email' ? 'Format email tidak valid. Contoh: nama@email.com' : 'Format isian tidak valid.';
    if (v.tooShort) return 'Minimal ' + el.minLength + ' karakter (saat ini ' + el.value.length + ').';
    if (v.tooLong) return 'Maksimal ' + el.maxLength + ' karakter.';
    if (v.patternMismatch) return el.dataset.patternMsg || 'Format isian tidak sesuai.';
    if (v.rangeUnderflow) return 'Nilai minimal ' + el.min + '.';
    if (v.rangeOverflow) return 'Nilai maksimal ' + el.max + '.';
    return 'Isian tidak valid.';
  }

  function sync(el) {
    if (!el.id) return;
    applyMatch(el);
    var feedback = document.getElementById(el.id + '-error');
    var ok = el.checkValidity();
    el.classList.toggle('is-invalid', !ok);
    if (ok) {
      el.removeAttribute('aria-invalid');
    } else {
      el.setAttribute('aria-invalid', 'true');
    }
    if (feedback) {
      feedback.textContent = ok ? '' : messageFor(el);
      var ids = (el.getAttribute('aria-describedby') || '').split(' ').filter(Boolean);
      var has = ids.indexOf(feedback.id) !== -1;
      if (!ok && !has) ids.push(feedback.id);
      if (ok && has) ids = ids.filter(function (i) { return i !== feedback.id; });
      if (ids.length) el.setAttribute('aria-describedby', ids.join(' '));
      else el.removeAttribute('aria-describedby');
    }
  }

  document.querySelectorAll('form[data-pit-validate]').forEach(function (form) {
    form.noValidate = true;
    var fields = form.querySelectorAll('input, select, textarea');

    fields.forEach(function (el) {
      var matchSel = el.getAttribute('data-match');
      if (matchSel) {
        var other = document.querySelector(matchSel);
        if (other) other.addEventListener('input', function () { if (el.dataset.touched) sync(el); });
      }
      el.addEventListener('blur', function () { if (el.value !== '' || el.dataset.touched) { el.dataset.touched = '1'; sync(el); } });
      el.addEventListener('input', function () { if (el.dataset.touched || el.classList.contains('is-invalid')) sync(el); });
      el.addEventListener('change', function () { if (el.dataset.touched || el.classList.contains('is-invalid')) sync(el); });
    });

    form.addEventListener('submit', function (event) {
      var firstInvalid = null;
      fields.forEach(function (el) {
        el.dataset.touched = '1';
        sync(el);
        if (!firstInvalid && !el.checkValidity()) firstInvalid = el;
      });

      var status = form.querySelector('[data-pit-form-status]');
      if (status) status.hidden = true;

      if (firstInvalid) {
        event.preventDefault();
        firstInvalid.focus();
        return;
      }

      // Simulasi (Fase 2): tanpa backend, form tidak dikirim ke mana pun.
      if (form.hasAttribute('data-pit-simulate')) {
        event.preventDefault();
        if (status) {
          status.hidden = false;
          status.focus();
        }
      }
    });
  });

  /* ---------- Tampil/sembunyikan kata sandi ---------- */

  document.querySelectorAll('[data-pit-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.querySelector(btn.getAttribute('data-pit-toggle-password'));
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.setAttribute('aria-pressed', show ? 'true' : 'false');
      btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
      btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
  });

  /* ---------- State loading pada tombol (button[data-pit-loading]) ---------- */
  // Simulasi 1,2 detik untuk memperlihatkan state loading. Backend nanti menggantikan timer ini.

  document.querySelectorAll('button[data-pit-loading]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (btn.disabled) return;
      var original = btn.innerHTML;
      btn.disabled = true;
      btn.setAttribute('aria-busy', 'true');
      btn.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span>Memproses…</span>';
      window.setTimeout(function () {
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
        btn.innerHTML = original;
      }, 1200);
    });
  });

  /* ---------- Filter grid (div[data-pit-filter]) ---------- */
  // Tombol [data-filter] menyaring anak grid berdasarkan atribut data-type.
  // Fase 3: data sementara dari MockCatalog; Fase 5 bisa diganti filter sisi server.

  document.querySelectorAll('[data-pit-filter]').forEach(function (group) {
    var grid = document.querySelector(group.getAttribute('data-pit-filter'));
    var status = document.querySelector(group.getAttribute('data-pit-filter-status'));
    var buttons = group.querySelectorAll('button[data-filter]');
    if (!grid) return;

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var wanted = btn.getAttribute('data-filter');
        var shown = 0;
        buttons.forEach(function (b) { b.setAttribute('aria-pressed', b === btn ? 'true' : 'false'); });
        grid.querySelectorAll('[data-type]').forEach(function (item) {
          var show = wanted === 'all' || item.getAttribute('data-type') === wanted;
          item.hidden = !show;
          if (show) shown += 1;
        });
        if (status) status.textContent = 'Menampilkan ' + shown + ' produk';
      });
    });
  });
})();
