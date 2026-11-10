# AGENTS.md — Pedoman wajib untuk AI agent dan developer

## A. Konteks
PITLABS: platform web penyewaan perangkat AI/IoT (sewa internal + beli via Shopee). Stack **Laravel + Blade + Bootstrap 5**. **Fase saat ini: frontend selesai sampai Fase 4; backend belum dikerjakan.** Ringkasan di [README.md](README.md), detail di [docs/PROJECT_DOCUMENTATION.md](docs/PROJECT_DOCUMENTATION.md).

## B. Aturan wajib
1. Baca [README.md](README.md), [docs/PROJECT_DOCUMENTATION.md](docs/PROJECT_DOCUMENTATION.md), [docs/DESIGN_SYSTEM.md](docs/DESIGN_SYSTEM.md), dan file ini **sebelum** mengubah apa pun.
2. Periksa struktur direktori dan implementasi aktual; jangan mengandalkan dokumentasi saja.
3. Jangan mengasumsikan fitur, struktur, atau teknologi yang belum diverifikasi.
4. Jangan mengubah desain yang sudah disetujui tanpa persetujuan eksplisit.
5. Jangan refactoring besar tanpa alasan dan persetujuan.
6. Jangan menghapus file/komponen yang masih dipakai tanpa memeriksa dependensinya (`grep`).
7. Perbarui dokumentasi bila perubahan memengaruhi sistem; catat di [docs/DEVELOPMENT_LOG.md](docs/DEVELOPMENT_LOG.md).
8. Jangan mengarang tarif, denda, spesifikasi, foto, kontak, atau URL. Pakai `<x-pending>` ("Informasi belum tersedia"). Daftar data: [docs/DATA_PRODUK_YANG_DIBUTUHKAN.md](docs/DATA_PRODUK_YANG_DIBUTUHKAN.md).
9. Katalog resmi hanya 7 produk. Jangan menambah produk atau memakai kata "sensor" pada headline.
10. Fitur AI tetap "Segera Hadir"; jangan membuat chatbot atau integrasi API AI.
11. Panduan penilaian: [docs/PJBL_CHECKLIST.md](docs/PJBL_CHECKLIST.md). Jangan menghapus elemen yang dibutuhkan penilaian.

## C. Konsistensi desain
Jangan mengganti palet, identitas visual, atau struktur layout; jangan membuat varian dashboard baru; jangan membuat komponen yang menduplikasi komponen yang ada. Gunakan token di `public/assets/css/pitlabs.css` dan variabel `--dash-*` di `dashboard.css`.
- Oranye **hanya** tombol Beli via Shopee.
- Tema admin (kuning) tidak boleh memengaruhi dashboard pelanggan; ubah hanya blok `.pit-theme-admin`.
- Teks di atas kuning memakai navy; cek kontras (≥ 4.5:1) untuk warna baru.
- Status order, pembayaran, pengambilan, penyewaan selalu terpisah dan memakai `<x-status-badge>`.

## D. Aturan pengembangan
- Blade dan komponen di `resources/views`; gunakan komponen yang ada sebelum membuat baru. Nama kelas berprefiks `pit-` (situs) atau `dash-` (dashboard); nama route berbahasa Inggris bertitik (`admin.orders.new`), URL berbahasa Indonesia.
- CSS per halaman di `public/assets/css/pages/`; hindari duplikasi dan `!important`. JS tanpa pustaka tambahan kecuali disetujui.
- Teks antarmuka Bahasa Indonesia. Aksesibilitas dasar wajib: label, fokus terlihat, target sentuh ≥ 44 px, `aria-*`.
- Controller tipis; data dari `app/Support/Mock*` sampai Fase 5.
- Setiap tautan/tombol harus berfungsi atau dijelaskan (jangan tombol mati).

## E. Pemisahan frontend dan backend
Saat ini **hanya frontend**. Dilarang tanpa persetujuan eksplisit: mengubah database, membuat migration, membuat API/logika bisnis, mengubah autentikasi, menghapus `DemoAuth`/`Mock*`. `DemoAuth`, `DemoRole`, dan `Mock*` ditandai sebagai simulasi dan akan diganti di Fase 5.

## F. Dokumentasi
Setiap pekerjaan selesai: perbarui [docs/DEVELOPMENT_LOG.md](docs/DEVELOPMENT_LOG.md) (fase, file terdampak, status, yang masih simulasi) dan dokumen terkait. Jangan mengklaim "selesai" jika belum diuji.

## G. Alur kerja (jangan dilewati)
1. **Audit:** baca dokumen di atas, periksa struktur dan implementasi, identifikasi yang selesai, belum, dan inkonsisten.
2. **Klarifikasi:** laporkan pemahaman, lalu ajukan hanya pertanyaan yang jawabannya belum ada di dokumentasi (target, fase, prioritas, perubahan requirement, boleh/tidak menyentuh backend).
3. **Konfirmasi:** buat rencana (tujuan, ruang lingkup, file terdampak, urutan, risiko, kriteria berhasil) dan **tunggu persetujuan**.
4. **Implementasi:** hanya sesuai ruang lingkup yang disetujui.
5. **Verifikasi dan dokumentasi:** periksa hasil, jalankan pemeriksaan relevan, pastikan tidak keluar ruang lingkup, perbarui log dan status fase.

Jangan melewati langkah 2–3 karena merasa sudah paham proyek.
