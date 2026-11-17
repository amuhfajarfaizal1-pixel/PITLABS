# AGENTS.md — Pedoman wajib untuk AI agent dan developer

## A. Konteks
PITLABS: platform web penyewaan perangkat AI/IoT (sewa internal + beli via Shopee). Stack **Laravel + Blade + Bootstrap 5**. **Fase saat ini: frontend selesai sampai Fase 4; backend dikerjakan bertahap sejak 5A (database + autentikasi).** Ringkasan di [README.md](README.md), detail di [docs/PROJECT_DOCUMENTATION.md](docs/PROJECT_DOCUMENTATION.md).

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
- Controller tipis; data dari `app/Support/Mock*` sampai Fase 5.
- Setiap tautan/tombol harus berfungsi atau dijelaskan (jangan tombol mati).

## E. Frontend dan backend
Pemilik telah mengizinkan backend **bertahap**, hanya untuk batch yang disetujui. Selesai: **5A** (migration users/categories/products/inventory_units, seeder, Laravel Auth, role CUSTOMER/ADMIN, profil dan ganti kata sandi, daftar pelanggan admin). **Belum disetujui:** katalog dari database + CRUD admin (5B), Google OAuth, booking dan checkout, payment gateway/webhook, pengambilan/pengembalian/denda/laporan.
- Kerjakan satu batch per persetujuan; jangan melompat ke batch berikutnya.
- Setiap batch menggantikan **satu** kelas `Mock*` tanpa mengubah tampilan; hapus kelas simulasi hanya setelah tidak ada yang memakainya (`grep`).
- `role`, `is_active`, `google_id` tidak mass-assignable; jangan diubah. Otorisasi di server (`auth` + `EnsureRole`), bukan hanya menyembunyikan menu.
- Kata sandi/rahasia tidak boleh ditulis di kode atau commit (`.env` tidak di-commit); repository GitHub bersifat publik. Akun admin dibuat manual lewat `php artisan pitlabs:create-admin`; jangan menambahkan pembuatan akun otomatis ke seeder bawaan. Tambahkan tes untuk perilaku baru. Kelas tes yang menyentuh database wajib memakai `RefreshDatabase`; fixture dengan email sama dalam satu tes tidak boleh dibuat dua kali (kolom `users.email` unik dan tidak boleh dilonggarkan).
- Perubahan skema lewat migration baru; jangan mengedit migration yang sudah dijalankan.
- Sejak 5C-1: booking, reservasi, invoice, pembayaran awal, dan pengaturan memakai database. Masih `Mock*`: halaman admin (pesanan, pembayaran, pengambilan, pengembalian, pemeriksaan, denda, laporan), antrean, dan KPI dashboard admin (5C-2 dan 5E). Produk, unit, dan pesanan tidak boleh dihapus (hanya nonaktif/pensiun).

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
