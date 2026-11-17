# AGENTS.md — Pedoman wajib untuk AI agent dan developer

## A. Konteks
PITLABS: platform web penyewaan perangkat AI/IoT (sewa internal + beli via Shopee). Stack **Laravel + Blade + Bootstrap 5**. **Status source per 17 November 2026: 5A–5B, 5C-1, dan implementasi source 5C-2/5E serta tema terang/gelap tersedia; uji integrasi masih wajib dilakukan pada instalasi Laravel lengkap.** Ringkasan di [README.md](README.md), detail di [docs/PROJECT_DOCUMENTATION.md](docs/PROJECT_DOCUMENTATION.md).

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
- Tema admin (Navy × Yellow: #0B1F3A, #07172B, #FACC15, #2563EB, #F4F6FA) tidak boleh memengaruhi dashboard pelanggan; ubah hanya blok `.pit-theme-admin`.
- Teks di atas kuning memakai navy; cek kontras (≥ 4.5:1) untuk warna baru.
- Status order, pembayaran, pengambilan, penyewaan selalu terpisah dan memakai `<x-status-badge>`.

## D. Aturan pengembangan
- Blade dan komponen di `resources/views`; gunakan komponen yang ada sebelum membuat baru. Nama kelas berprefiks `pit-` (situs) atau `dash-` (dashboard); nama route berbahasa Inggris bertitik (`admin.orders.new`), URL berbahasa Indonesia.
- CSS per halaman di `public/assets/css/pages/`; hindari duplikasi dan `!important`. JS tanpa pustaka tambahan kecuali disetujui.
- Teks antarmuka Bahasa Indonesia. Aksesibilitas dasar wajib: label, fokus terlihat, target sentuh ≥ 44 px, `aria-*`.
- Controller sebaiknya tipis; halaman yang sudah dialihkan ke backend harus menggunakan database, bukan `app/Support/Mock*`. Kelas Mock legacy hanya dipertahankan jika masih digunakan halaman yang belum dialihkan dan tidak boleh menjadi sumber data transaksi/dashboard operasional.
- Setiap tautan/tombol harus berfungsi atau dijelaskan (jangan tombol mati).

## E. Frontend dan backend
Pemilik mengizinkan backend bertahap. Status historis dan source saat ini: **5A** (database dasar, autentikasi, role, profil), **5B** (katalog database, CRUD produk/unit dan foto), **5C-1** (booking, reservasi, checkout COD, invoice dan QR token) telah diimplementasikan pada source terdahulu. Pada 17 November 2026, pemilik secara eksplisit mengizinkan batch gabungan implementasi **5C-2/5E/tema**: verifikasi pembayaran COD, scanner, pickup/return/inspeksi/denda/antrean, dashboard/laporan DB, proteksi detail produk, light/dark mode. Semua implementasi baru menunggu uji integrasi pada instalasi lengkap. Google OAuth dan payment gateway/webhook tetap belum tersedia karena provider/kredensial belum diberikan.
- Kerjakan sesuai cakupan yang telah disetujui; untuk fitur/keputusan baru di luar cakupan, minta persetujuan.
- Setiap kelas `Mock*` hanya boleh dipensiunkan setelah semua pemakai diperiksa (`grep`); halaman operasional yang sudah memakai DB tidak boleh fallback ke data contoh.
- `role`, `is_active`, `google_id` tidak mass-assignable; jangan diubah. Otorisasi di server (`auth` + `EnsureRole`), bukan hanya menyembunyikan menu.
- Kata sandi/rahasia tidak boleh ditulis di kode atau commit (`.env` tidak di-commit); repository GitHub bersifat publik. Akun admin dibuat manual lewat `php artisan pitlabs:create-admin`; jangan menambahkan pembuatan akun otomatis ke seeder bawaan. Tambahkan tes untuk perilaku baru. Kelas tes yang menyentuh database wajib memakai `RefreshDatabase`; fixture dengan email sama dalam satu tes tidak boleh dibuat dua kali (kolom `users.email` unik dan tidak boleh dilonggarkan).
- Perubahan skema lewat migration baru; jangan mengedit migration yang sudah dijalankan.
- Sejak 5C-1: booking, reservasi, invoice, pembayaran awal, dan pengaturan memakai database. Source 17-11 menambahkan implementasi 5C-2/5E untuk verifikasi COD, scanner invoice, pickup/return/inspection/fines/waitlist, dashboard/laporan DB, proteksi detail produk dan light/dark mode; integrasi masih menunggu uji pada instalasi lengkap. QRIS/VA, gateway/webhook dan notifikasi eksternal belum tersedia. Produk, unit, dan pesanan tidak boleh dihapus (hanya nonaktif/pensiun).

## F. Dokumentasi
Setiap pekerjaan selesai: perbarui [docs/DEVELOPMENT_LOG.md](docs/DEVELOPMENT_LOG.md) (fase, file terdampak, status, yang masih simulasi) dan dokumen terkait. Jangan mengklaim "selesai" jika belum diuji.

## G. Alur kerja (jangan dilewati)
1. **Audit:** baca dokumen di atas, periksa struktur dan implementasi, identifikasi yang selesai, belum, dan inkonsisten.
2. **Klarifikasi:** laporkan pemahaman, lalu ajukan hanya pertanyaan yang jawabannya belum ada di dokumentasi (target, fase, prioritas, perubahan requirement, boleh/tidak menyentuh backend).
3. **Konfirmasi:** buat rencana (tujuan, ruang lingkup, file terdampak, urutan, risiko, kriteria berhasil) dan **tunggu persetujuan**.
4. **Implementasi:** hanya sesuai ruang lingkup yang disetujui.
5. **Verifikasi dan dokumentasi:** periksa hasil, jalankan pemeriksaan relevan, pastikan tidak keluar ruang lingkup, perbarui log dan status fase.

Jangan melewati langkah 2–3 karena merasa sudah paham proyek.

## H. Aturan kerja dari pemilik (mengikat)
1. **Jangan mengambil keputusan sepihak.** Bila instruksi kurang jelas, ada beberapa kemungkinan implementasi, informasi belum tersedia, ada konflik dengan fitur lama, perubahan database yang berdampak, ide tambahan, atau kendala yang mengubah pendekatan: **berhenti di bagian itu** dan jelaskan (a) masalahnya, (b) mengapa perlu keputusan, (c) pilihan, (d) kelebihan dan kekurangan tiap pilihan, (e) rekomendasi. Tunggu persetujuan; diam bukan persetujuan. Keputusan teknis kecil yang jelas tercakup dalam instruksi boleh diambil.
2. **Setiap selesai satu bagian, wajib memberi `CHECKLIST HASIL PENGEMBANGAN`** (A fitur dikerjakan, B bagian yang perlu diperiksa pemilik, C perubahan teknis, D status pengujian, E hal yang butuh konfirmasi, F status keseluruhan) **dan panduan pemeriksaan langkah demi langkah** yang hanya mencakup pekerjaan baru dan bagian terdampak.
3. **Jangan menganggap pekerjaan disetujui.** Koreksi pemilik diperbaiki tanpa mengubah bagian lain yang sudah sesuai; bila koreksi menimbulkan ketidakjelasan, tanyakan.
4. **Komunikasi terbuka:** laporkan fitur belum selesai, bug, fungsi yang masih data contoh, bagian yang belum terhubung database, pengujian yang belum dilakukan, kendala, dan perubahan di luar instruksi. Jangan mengklaim berhasil bila belum.
5. **Status yang dipakai:** Belum dikerjakan, Sedang dikerjakan, Sudah diimplementasikan, Sudah diuji, Perlu perbaikan. Bedakan "kode ditulis", "diimplementasikan", dan "diuji".
6. **Pengiriman hasil:** kirim hanya file yang berubah atau baru beserta jalur tujuannya, bukan satu zip utuh.
7. **Dokumentasi untuk logbook:** catat tiap bagian yang benar-benar selesai di `docs/DEVELOPMENT_LOG.md`, `docs/LOGBOOK_NOTES.md`, `docs/REPORT_NOTES.md`, `docs/SCREENSHOT_GUIDE.md`. Jangan mengarang tanggal atau kegiatan; tanggal logbook memakai WITA (log sandbox memakai UTC); yang tidak dapat dipastikan ditandai "belum terverifikasi". Target mingguan mengacu pada spreadsheet logbook pemilik dan tidak boleh diubah urutannya; pekerjaan yang mendahului jadwal dicatat sebagai "dipercepat".
8. **Prioritas pemilik:** (1) backend sesuai target logbook, (2) scan invoice yang benar-benar berfungsi (kamera dan input manual, terhubung database, pemindaian tidak mengubah status transaksi otomatis), (3) light/dark mode di seluruh halaman dengan identitas dashboard tetap berbeda, (4) pengujian dan perbaikan bug, (5) dokumentasi.


## Status persetujuan pemilik — 17 November 2026
Pemilik secara eksplisit meminta pengerjaan langsung seluruh cakupan tersisa: scanner QR kamera/manual dan verifikasi DB, backend pembayaran/pengambilan/pengembalian/pemeriksaan/denda, dashboard/laporan database, dan light/dark mode. Cakupan source 17-11 telah diimplementasikan sebagai patch. Pengujian integrasi belum dapat dinyatakan selesai sampai dijalankan pada instalasi Laravel penuh.
