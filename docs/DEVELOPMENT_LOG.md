# Development Log — PITLABS

## 2026-11-10 — Fase 5A: database dan autentikasi
- Disetujui pemilik: backend dimulai dari database + autentikasi; PHP pemilik dilaporkan 8.5.x (perlu dikonfirmasi `php -v`); database `pitlabs` di MySQL XAMPP; payment gateway ditunda.
- Added: migration (extend `users`, `categories`, `products`, `inventory_units`), model `User`/`Category`/`Product`/`InventoryUnit`, seeder (kategori, 7 produk, 15 unit contoh, akun admin dan pelanggan contoh), `EnsureRole`, `tests/Feature/AuthTest.php` (11 tes).
- Changed: `AuthController` (login/registrasi sungguhan, throttle per email+IP, pesan login generik, regenerasi session), `routes/web.php` (`auth` + `EnsureRole`, rute profil dan kata sandi, aktif/nonaktif pelanggan), `CustomerController` (profil dan kata sandi nyata), `AdminController` (daftar pelanggan dari DB), komponen `dash.table` (aksi form POST), views auth/navbar/layout dashboard/invoice/availability (`auth()`), halaman login/register tanpa banner demo, modal Google menjadi informasi.
- Removed: `DemoAuth`, `DemoRole`, login demo dan tombol akses cepat.
- Belum diuji: seluruhnya belum dijalankan (sandbox tanpa PHP/MySQL). Cek statis: sintaks JS, pasangan direktif Blade, nama route. Mohon jalankan `php artisan migrate --seed` dan `php artisan test`, lalu laporkan hasilnya.
- Masih `Mock*`: katalog, ketersediaan, pesanan, antrean, laporan. Login Google: 5B.
- Berikutnya (usulan, menunggu persetujuan): 5B katalog dari database + CRUD produk/unit admin.

## 2026-11-10 — Fase 4B: dashboard admin dan pelanggan, checkout, invoice, halaman informasi
- Added: kerangka dashboard bersama (`layouts/dashboard`, sidebar offcanvas, topbar, notifikasi) dengan dua tema: user (brand Sapphire/Navy, tidak diubah) dan admin (Bright Yellow × Charcoal/Navy × netral) — beda hanya di variabel `--dash-*` (`public/assets/css/dashboard.css`).
- Added (admin, 12 halaman): dashboard, New Orders, Ongoing Rentals, verifikasi pembayaran, QR Scanner, verifikasi pengambilan, pengembalian, pemeriksaan kondisi, pengelolaan denda, produk, inventaris, laporan keuangan.
- Added (pelanggan): dashboard, penyewaan aktif, riwayat, informasi pengembalian, profil, checkout, invoice (dapat dicetak).
- Added (publik): FAQ, Panduan, Tentang kami, Artikel; bagian ketentuan sewa dipisah ke `partials/ketentuan-sewa` dan dipakai di homepage dan Panduan.
- Added: komponen `dash.*` (page-header, stat, panel, table, bars), `DemoRole` middleware, `MockData`, akses cepat "Admin demo / Pelanggan demo" di halaman login.
- Masih simulasi: login/role, semua aksi tabel (modal konfirmasi + toast, tidak disimpan), checkout (pesanan di session), QR pada invoice (pola contoh, tidak dapat dipindai), nama/invoice/nominal pada dashboard (FIKTIF). Nominal denda dan tarif tetap "Informasi belum tersedia".
- Belum diuji di browser. Cek statis: sintaks JS, pasangan direktif Blade, nama route.
- Keputusan pemilik: halaman AI hanya "Segera Hadir"; kategori/artikel "Sensor" diganti yang sesuai 7 produk; tab Pelanggan dan Antrean Rental dari prototipe native dimasukkan; tampilan Fase 2–3 yang sudah disetujui dipertahankan.
- Added: `/asisten-ai` (Segera Hadir, form dinonaktifkan), admin Pelanggan dan Antrean rental, pelanggan Antrean rental, tombol "Masuk antrean" saat semua unit bentrok (simulasi di session).
- Added: README.md, docs/PROJECT_DOCUMENTATION.md, AGENTS.md; DESIGN_SYSTEM.md bagian dashboard.

## 2026-11-10 — Fase 4A: katalog, detail, ketersediaan, login/registrasi, SEO dasar
- Added: halaman `/katalog` (cari + filter jenis + filter unit tersedia), `/produk/{slug}`, `/cek-ketersediaan`, `/masuk`, `/daftar`, `/sitemap.xml`, `public/robots.txt`.
- Added: login Google (UI + alur simulasi), logout (POST), menu pengguna di navbar, `x-breadcrumb` (+ BreadcrumbList JSON-LD), toggle tampil kata sandi, validasi konfirmasi kata sandi.
- Added: `DemoAuth`, `MockAvailability` (aturan bentrok dari BUSINESS_RULES §2), controller Catalog/Availability/Auth/Seo, `docs/PJBL_CHECKLIST.md`, `docs/DATA_PRODUK_YANG_DIBUTUHKAN.md`.
- Changed: `config/pitlabs.php` (status unit dan ketersediaan, `demo_mode`), layout (pesan status, `defer` JS), ukuran logo.
- Masih simulasi: seluruh autentikasi (tidak ada akun/hash/database), login Google, jadwal booking contoh, status unit contoh, `sale_enabled` dan `rental_enabled` bernilai demo.
- Belum ada: halaman checkout, invoice, dashboard, riwayat, penyewaan aktif, informasi pengembalian (Fase 4B); foto produk dan data produk.
- Belum diuji: tampilan di browser dan semua alur. Cek yang sudah dilakukan: sintaks JS, kurung CSS/PHP, pasangan direktif Blade.
- Berikutnya: Fase 4B (checkout, invoice, dashboard customer, riwayat, penyewaan aktif, pengembalian) setelah 4A diperiksa.

## 2026-11-10 — Fase 3: homepage
- Added: `home.blade.php`, `HomeController`, `app/Support/MockCatalog.php` (data sementara 7 produk, tanpa harga/spesifikasi), `app/Support/Pit.php` (helper tautan aman untuk route yang belum ada), komponen `product-card` dan `terms-checkbox`, `public/assets/css/pages/home.css`.
- Changed: `routes/web.php` (`/` → `HomeController`), `pitlabs.css` (`.pit-link`, accordion), `pitlabs.js` (filter grid), `design-system.blade.php` (checkbox memakai `x-terms-checkbox`).
- Fase 2 disetujui pemilik. Permintaan tambahan: teks "ketentuan sewa dan denda kerusakan/kehilangan" pada checkbox menjadi tautan biru menuju bagian ketentuan (`#ketentuan-sewa`, dibuka di tab baru).
- Interaksi berfungsi: filter jenis produk, accordion FAQ, modal AI "Segera Hadir", tautan ke bagian ketentuan.
- Masih simulasi/belum ada: kartu produk tanpa foto (ubin ikon), semua harga dan nilai ketentuan tampil "Informasi belum tersedia", tautan katalog/detail/registrasi bergantung route Fase 4 (saat belum ada: fallback anchor atau tampil nonaktif), kartu "Contoh tampilan dashboard" adalah ilustrasi.
- Tidak dikerjakan: bagian Artikel di landing (belum ada konten), foto produk.
- Belum diuji: tampilan di browser (dilakukan pemilik), responsivitas 320/375/768/1024/1440 px, pembaca layar.
- Berikutnya: Fase 4 — katalog, detail produk, dan halaman customer lain (menunggu persetujuan Fase 3).

## 2026-11-09 — Fase 2: design system (frontend)
- Added: `public/assets/css/pitlabs.css` (token + override Bootstrap), `public/assets/js/pitlabs.js` (validasi form sisi klien, simulasi submit, state loading), `config/pitlabs.php` (identitas, navigasi, label status).
- Added: layout `layouts/app`, partial `navbar`, `footer`, `ai-assistant`; komponen `status-badge`, `field`, `modal`, `alert`, `empty-state`, `pending`; halaman referensi `/design-system` (hanya `local`).
- Changed: `routes/web.php` — route `/` diberi nama `home`; route `/design-system` ditambahkan.
- Documentation updated: `DESIGN_SYSTEM.md` (baru).
- Masih simulasi: submit form demo (tidak dikirim), tombol loading (timer 1,2 detik), tautan navigasi ke halaman yang belum dibuat (tampil nonaktif dengan keterangan).
- Belum diuji: tampilan di browser, responsivitas 320/375/768/1024/1440 px, pembaca layar. Pengecekan yang sudah dilakukan: sintaks JS, keseimbangan kurung CSS, perhitungan kontras warna.
- Berikutnya: Fase 3 — homepage mengikuti referensi hero (setelah Fase 2 diperiksa dan disetujui).

## 2026-11-09 — Fase 1: analisis referensi
- Dipelajari: Documentation Pack, panduan PjBL (PDF), prototype statis HTML/CSS, prototype PHP + MySQL (XAMPP), repository GitHub.
- Keputusan: stack final **Laravel (Blade) + Bootstrap 5** (sesuai ketentuan PjBL dan `ARCHITECTURE.md`); kode PHP native lama dijadikan referensi alur, bukan dasar kode.
- Ditandai untuk fase berikutnya: ganti data demo prototype (produk DHT22/ESP32, kategori Sensor, harga karangan) dengan 7 produk resmi dan "Informasi belum tersedia"; amankan `config.php` dan folder `pitlabs_private` sebelum repo dipublikasikan lebih lanjut.

## 2026-11-09 — Documentation baseline
- Menggabungkan brief hybrid e-commerce + rental dengan revisi dosen/mitra.
- Menetapkan pemisahan status order, pembayaran, pickup, dan rental.
- Menetapkan invoice dibuat sejak order terbentuk, termasuk COD.
- Menetapkan QR invoice untuk verifikasi admin.
- Menetapkan inventaris per kode unit dan pencegahan bentrok periode.
- Menetapkan inspeksi admin sebelum penetapan denda/penyelesaian rental.
- Menambahkan baseline PRD, business rules, UI/UX, architecture, database, API, workflow, task board, dan test plan.
- AI Assistant dipertahankan sebagai UI; backend AI ditunda.
- Nilai tarif, denda, alamat pickup, URL Shopee, dan kebijakan hold masih menunggu konfirmasi mitra.

## Template perubahan berikutnya
### YYYY-MM-DD — Judul
- Added:
- Changed:
- Fixed:
- Database:
- API:
- Tests:
- Documentation updated:
