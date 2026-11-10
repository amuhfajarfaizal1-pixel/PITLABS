/*
 * Dashboard PITLABS — interaksi bersama (Fase 4B)
 * 1) Cari + filter tabel  2) Aksi simulasi dengan modal konfirmasi  3) Toast
 * Semua aksi SIMULASI: tampilan berubah, data tidak disimpan.
 */
(function () {
  'use strict';

  /* ---------- Toast ---------- */
  function toast(message) {
    var el = document.getElementById('dashToast');
    if (!el || !window.bootstrap) return;
    document.getElementById('dashToast-text').textContent = message;
    window.bootstrap.Toast.getOrCreateInstance(el, { delay: 3500 }).show();
  }
  window.pitToast = toast;

  /* ---------- Cari + filter tabel ---------- */
  document.querySelectorAll('[data-pit-table]').forEach(function (wrap) {
    var table = document.querySelector(wrap.getAttribute('data-pit-table'));
    if (!table) return;
    var search = wrap.querySelector('[data-pit-table-search]');
    var filters = wrap.querySelectorAll('[data-pit-table-filter]');
    var count = wrap.querySelector('[data-pit-table-count]');
    var empty = wrap.querySelector('[data-pit-table-empty]');
    var rows = Array.prototype.slice.call(table.tBodies[0].rows).filter(function (r) { return r.hasAttribute('data-search'); });

    function apply() {
      var q = search ? search.value.trim().toLowerCase() : '';
      var shown = 0;
      rows.forEach(function (row) {
        var ok = !q || row.getAttribute('data-search').indexOf(q) !== -1;
        filters.forEach(function (f) {
          var v = f.value;
          if (v && row.getAttribute('data-' + f.getAttribute('data-key')) !== v) ok = false;
        });
        row.hidden = !ok;
        if (ok) shown += 1;
      });
      if (count) count.textContent = shown + ' dari ' + rows.length + ' data';
      if (empty) empty.hidden = shown !== 0 || rows.length === 0;
      table.closest('.table-responsive').hidden = shown === 0 && rows.length > 0;
    }

    if (search) search.addEventListener('input', apply);
    filters.forEach(function (f) { f.addEventListener('change', apply); });
    apply();
  });

  /* ---------- Aksi simulasi ---------- */
  var modalEl = document.getElementById('simModal');
  var pending = null;

  document.querySelectorAll('[data-pit-sim]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (!modalEl || !window.bootstrap) return;
      pending = btn;
      document.getElementById('simModal-title').textContent = btn.getAttribute('data-sim-title') || 'Konfirmasi';
      document.getElementById('simModal-text').textContent = btn.getAttribute('data-sim-text') || '';
      document.getElementById('simModal-note').value = '';
      window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
  });

  var confirmBtn = document.getElementById('simModal-confirm');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      if (!pending) return;
      var btn = pending;
      var row = document.querySelector(btn.getAttribute('data-sim-row'));
      if (row) {
        row.querySelectorAll('[data-sim-before]').forEach(function (n) { n.hidden = true; });
        row.querySelectorAll('[data-sim-after]').forEach(function (n) { n.hidden = false; });
        row.querySelectorAll('[data-sim-action]').forEach(function (n) { n.hidden = true; });
        row.classList.add('is-updated');
        window.setTimeout(function () { row.classList.remove('is-updated'); }, 1600);
      }
      window.bootstrap.Modal.getInstance(modalEl).hide();
      toast((btn.getAttribute('data-sim-message') || 'Berhasil') + ' (simulasi, belum disimpan)');
      pending = null;
    });
  }

  /* ---------- Cari invoice (halaman scanner) ---------- */
  var lookup = document.querySelector('[data-pit-invoice-lookup]');
  if (lookup) {
    var input = lookup.querySelector('input');
    var result = document.querySelector(lookup.getAttribute('data-pit-invoice-lookup'));
    var notFound = document.querySelector(lookup.getAttribute('data-not-found'));
    lookup.addEventListener('submit', function (e) {
      e.preventDefault();
      var code = input.value.trim().toUpperCase();
      var found = false;
      result.querySelectorAll('[data-invoice]').forEach(function (card) {
        var match = code && card.getAttribute('data-invoice') === code;
        card.hidden = !match;
        if (match) found = true;
      });
      if (notFound) notFound.hidden = found || !code;
      if (!code) input.focus();
    });
  }

  /* ---------- Bagian bersyarat: [data-show-when="namaRadio:nilai1,nilai2"] ---------- */
  document.querySelectorAll('[data-show-when]').forEach(function (box) {
    var spec = box.getAttribute('data-show-when').split(':');
    var radios = document.querySelectorAll('input[name="' + spec[0] + '"]');
    var values = spec[1].split(',');
    function update() {
      var checked = document.querySelector('input[name="' + spec[0] + '"]:checked');
      var show = !!checked && values.indexOf(checked.value) !== -1;
      box.hidden = !show;
      box.querySelectorAll('[data-required-when-visible]').forEach(function (f) { f.required = show; });
    }
    radios.forEach(function (r) { r.addEventListener('change', update); });
    update();
  });
})();
