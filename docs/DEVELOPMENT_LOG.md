## 2026-11-17 — Fase 5C-2, 5E, pemindaian invoice dan tema (source; menunggu uji integrasi)
- Cakupan yang diminta pemilik: booking tersimpan ke database, detail produk wajib login, scanner QR kamera dan manual dengan verifikasi database (scan tidak mengubah status), backend operasional pembayaran/pengambilan/pengembalian/inspeksi/denda, antrean, dashboard/laporan transaksi nyata, serta tema terang/gelap.
- Added: migration rekonsiliasi non-destruktif untuk tabel rental inti yang hilang meskipun migration lama tercatat; migration kolom serah-terima/pengembalian serta tabel inspeksi, denda, antrean.
- Added: controller operasional database; kamera QR berbasis `BarcodeDetector` browser + input manual; verifikasi HMAC dan hash token invoice; aksi pickup terpisah setelah verifikasi dan pembayaran lunas, dengan masa berlaku hasil scan lima menit; pemeriksaan kondisi dan bukti file privat; unit ditahan dalam status perawatan sampai pemeriksaan selesai; denda tanpa nilai default dan pencatatan lunas dengan nominal yang harus cocok; dashboard/laporan dari pembayaran terverifikasi; tema tersimpan di browser dan mengikuti preferensi OS bila belum dipilih.
- Safety detail: setelah pengembalian, unit ditahan pada status `MAINTENANCE` sampai inspeksi menyatakan kondisi `GOOD`; nominal pembayaran denda yang ditandai lunas wajib sama dengan nominal yang telah ditetapkan. KPI ketersediaan hari ini menghitung unit sibuk secara unik dan hanya unit dari produk aktif yang dapat disewa.
- Changed: detail produk dilindungi middleware `auth` dan login kembali ke URL tujuan; `DatabaseSeeder` tidak lagi membuat produk, unit, pelanggan atau transaksi contoh; invoice tidak menampilkan QR dummy; state antrean mendukung diberi tahu/terpenuhi/dibatalkan.
- Safety/business rules: COD saja yang dapat diverifikasi manual; QRIS/VA belum tersambung provider. Nominal yang belum tersedia diisi hanya berdasarkan uang yang benar-benar diterima/keputusan resmi admin, bukan dibuat aplikasi. Denda ditahan pada status review tanpa nominal sampai admin memiliki kebijakan mitra. Produk/unit/pesanan tetap tidak dapat dihapus permanen sesuai `AGENTS.md`.
- File terdampak: `app/Http/Controllers/{AdminController,AuthController,CustomerController}.php`, model rental operation, migrations 17 Nov, `routes/web.php`, `config/pitlabs.php`, seeder, view admin/invoice/layout/navbar/table, assets JS/CSS, tests, README dan docs.
- Verifikasi penyusunan: PHP lint dan pemeriksaan sintaks JS dijalankan setelah patch. **Integrasi Artisan/MySQL dan PHPUnit belum dijalankan**, karena arsip yang diberikan adalah source overlay tanpa `artisan`, `composer.json`, `composer.lock`, `bootstrap/`, `.env`, dan `vendor/`. Jangan anggap fitur sudah diuji atau siap produksi sebelum pengujian instalasi lengkap.

# Development Log — PITLABS

## 2026-11-15 — Fase 5C-1: booking, checkout, invoice ber-QR (belum diuji)
- Keputusan pemilik: (1A) parameter sewa dikosongkan dulu dan diisi admin; (2) scan invoice nyata dengan QR dan kamera, QR disusun supaya admin cukup memindai lalu mengonfirmasi; (3) tema mengikuti perangkat dan dapat diubah; (4) deploy ditunda; (5) pemetaan minggu dikonfirmasi.
- Added: migration `settings`, `rental_orders`, `rental_order_items`, `reservations`, `invoices`, `payments`, `audit_logs` (mengikuti `schema.sql`; kolom uang dibuat NULLABLE karena tarif belum ditetapkan: penyimpangan dari docs yang disengaja).
- Added: model `RentalOrder`, `RentalOrderItem`, `Reservation`, `Invoice`, `Payment`, `AuditLog`, `Setting`; layanan `App\Support\BookingService` (satu transaksi, kunci baris unit, tolak bentrok), `Availability` (ketersediaan dari reservasi), `InvoiceToken` (HMAC-SHA256 dengan APP_KEY, hash SHA-256 disimpan), `InvoiceQr` (SVG, paket `bacon/bacon-qr-code`), `BookingException`.
- Added: halaman admin Pengaturan (`/admin/pengaturan`), komponen `x-setting`, tes `BookingTest` (9), `SettingsTest` (4), `InvoiceTokenTest` (4).
- Changed: checkout menyimpan pesanan nyata (hanya COD; QRIS dan VA ditandai Segera Hadir), invoice dari database (hanya pemilik atau admin; nomor lain 404), dashboard pelanggan dari pesanan nyata, ketersediaan dan katalog dari reservasi, durasi min/maks dari pengaturan, alamat dan jam pengambilan dari pengaturan (footer, ketentuan, invoice).
- Perilaku yang perlu dikonfirmasi: pemindaian QR hanya menampilkan data; perubahan status pengambilan lewat satu tombol Konfirmasi (sesuai aturan "pemindaian tidak mengubah status otomatis"); layanan digital belum dapat dipesan; admin belum melihat pesanan nyata sampai 5C-2.
- Belum diuji: seluruhnya. Wajib: `composer require bacon/bacon-qr-code`, `php artisan migrate`, `php artisan test`.
- Masih data contoh: halaman admin (New Orders, pembayaran, pengambilan, pengembalian, pemeriksaan, denda, laporan, antrean).

> Catatan: tanggal pada log teknis ini mengikuti jam sandbox (UTC). Untuk logbook gunakan WITA (UTC+8) pada `LOGBOOK_NOTES.md`.

## 2026-11-12 — Aturan kerja baru, dokumentasi logbook, panduan deploy FTP
- Pemilik menetapkan aturan kerja baru (tanya sebelum memutuskan, checklist setiap bagian, status pengujian jelas, dokumentasi untuk logbook) dan prioritas: backend sesuai logbook, scan invoice nyata, light/dark mode, pengujian, dokumentasi. Semuanya dicatat di `AGENTS.md` bagian H.
- Pemilik mengirim screenshot spreadsheet logbook: M1 ide dan wawancara mitra, M2–M3 desain UI/UX, M4–M5 fitur utama + CRUD + database + SEO, M6 integrasi pihak ketiga + pengujian. Pemetaan minggu ke tanggal bersifat kesimpulan (belum terverifikasi); ditemukan tulisan "Kamis, 27 Oktober 2026" yang seharusnya Selasa.
- Added: `docs/LOGBOOK_NOTES.md`, `docs/REPORT_NOTES.md`, `docs/SCREENSHOT_GUIDE.md`, `docs/DEPLOY_FTP.md`.
- Changed: `README.md` (tautan dokumen), `AGENTS.md` (bagian H).
- Status: dokumentasi ditulis; tidak ada perubahan kode. Pengerjaan 5C, scan invoice, dan light/dark mode **menunggu keputusan pemilik** (lihat pesan pengembangan).
- Kendala: tidak ada akses ke riwayat Git dan tanggal pekerjaan pemilik di luar percakapan, sehingga beberapa entri logbook ditandai belum terverifikasi.

## 2026-11-12 — Perbaikan: tes tidak dapat dimuat; APP_KEY hilang
- Lingkungan pemilik: PHP 8.5.12, Laravel 13.40.0.
- **`php artisan test` fatal error:** `AdminAccountTest::options()` private bentrok dengan `TestCase::options()` (helper request HTTP OPTIONS) → seluruh suite gagal dimuat. Diperbaiki: diganti `adminArgs()`. `SmokeTest::login()` diganti `masukSebagai()` sebagai pencegahan. Seluruh nama helper pada tes diperiksa terhadap daftar metode TestCase.
- **`MissingAppKeyException` pada `GET /`:** `APP_KEY` kosong di `.env` pemilik (bukan bug kode). Perbaikan: `php artisan key:generate` lalu `php artisan config:clear`. Ditambahkan ke `docs/TROUBLESHOOTING.md` bersama penjelasan galat nama helper tes.
- Peringatan `Module "openssl" is already loaded` masih muncul pada lingkungan pemilik (konfigurasi PHP, lihat TROUBLESHOOTING bagian 1); tidak menghentikan aplikasi.

## 2026-11-12 — Foto produk dan perbaikan gaya kartu
- Keputusan pemilik: tidak perlu Figma/user flow baru (Figma sudah ada di tahap awal); 5C tetap ditunda menunggu data mitra. Dikerjakan pekerjaan yang tidak bergantung pada mitra.
- **Bug ditemukan dan diperbaiki:** gaya kartu produk (`.pit-product*`) hanya ada di `pages/home.css`, sehingga kartu di `/katalog` dan "Produk lainnya" pada halaman detail tampil tanpa gaya. Dipindah ke `pitlabs.css` (global). Tes: `test_gaya_kartu_produk_dimuat_di_katalog_dan_detail`.
- Added: kolom `products.image_path` dan `image_alt` (migration `2026_11_12_100001`), unggah/ganti/hapus foto pada form produk admin, foto di kartu dan detail (lazy-load untuk kartu, `fetchpriority=high` untuk detail), `og:image`/`og:url`/`twitter:card` di layout, `public/uploads/` + `.htaccess` (tanpa eksekusi skrip), `.gitignore` untuk foto.
- Keamanan unggahan: validasi `mimes:jpg,jpeg,png,webp` berdasarkan isi berkas (SVG dan berkas menyamar `.png` ditolak), maks. 2 MB, nama berkas UUID dengan ekstensi dari isi berkas, hapus berkas memakai `basename()` (anti path traversal), folder tanpa eksekusi skrip.
- Tes baru: 5 (tiga di `AdminCatalogTest`, dua di `CatalogTest`). Unggahan tes memakai folder sementara.
- Perlu dijalankan pemilik: `php artisan migrate` (migration baru) lalu `php artisan test`. Belum diuji oleh penulis (tanpa PHP).

## 2026-11-11 — Akun admin manual; 5C ditunda
- Permintaan pemilik: akun admin tidak dibuat otomatis; dibuat manual. 5C ditunda sampai ada data dari mitra.
- Removed: `UserSeeder` (membuat admin dan pelanggan contoh otomatis saat `migrate --seed`).
- Added: `php artisan pitlabs:create-admin` (`App\Console\Commands\CreateAdmin`; prompt tersembunyi, `--reset`, `--promote`), `App\Support\AdminAccount`, `AdminSeeder` manual (membaca `.env` lewat `config('pitlabs.admin_seed')`), `tests/Feature/AdminAccountTest.php` (6 tes).
- Keputusan keamanan: kata sandi admin TIDAK ditulis di berkas kode karena repository publik; jalur yang disediakan adalah prompt Artisan dan `.env` (tidak di-commit).
- Catatan lingkungan yang sudah ada: akun admin dari seeding lama tetap ada di database. Atur ulang dengan `php artisan pitlabs:create-admin --email=... --reset`.
- Belum diuji oleh penulis (tanpa PHP). Mohon `php artisan test`.

## 2026-11-11 — Alur pembaruan dan GitHub
- Permintaan pemilik: tidak ingin `composer create-project` setiap pembaruan; push ke GitHub sering bermasalah; minta roadmap.
- Added: `scripts/update-pitlabs.ps1` (terapkan zip tanpa Composer, hapus berkas usang, `optimize:clear`, opsi `-Migrate`), `scripts/git-publish.ps1` (pengaman `.env`/`vendor`, commit, `pull --rebase`, push), `.gitignore`, `.gitattributes`, `docs/GIT_WORKFLOW.md`, `docs/ROADMAP.md`; README bagian "Update & Git Workflow".
- Catatan: Composer tidak dapat dihilangkan dari Laravel, tetapi hanya perlu sekali per komputer. Skrip PowerShell belum dijalankan di lingkungan penulis (tanpa PowerShell) dan hanya ditinjau manual; mohon dicoba dan laporkan galatnya.
- Dugaan akar masalah push: repository berisi PHP native (riwayat dan struktur berbeda dari Laravel). Penyelesaian: cabang baru `laravel` (tidak destruktif) atau penggantian `main` dengan cadangan `legacy-native`. Galat sebenarnya belum diketahui; menunggu pesan galat dari pemilik.

## 2026-11-11 — Perbaikan Fase 5: hero, tema admin, tes, OpenSSL
- Laporan pemilik: `php artisan test` → 22 passed, 3 failed.
- **Tes, akar masalah:** (A, B) fixture `admin()`/`user()` membuat pengguna dengan email tetap; tes yang memanggilnya dua kali melanggar `users.email` unik (perilaku constraint benar, fixture yang salah). Diperbaiki: satu admin per tes (`AdminCatalogTest`) dan fixture `user()` menerima email eksplisit untuk aktor kedua (`AuthTest`). (C) `ExampleTest` bawaan Laravel tidak memakai `RefreshDatabase`, sedangkan beranda kini membaca tabel `products` pada SQLite in-memory kosong. Diperbaiki dengan `RefreshDatabase` (migration dijalankan, data terisolasi). Tidak ada assertion dihapus, tes dimatikan, atau constraint dilonggarkan; `assertSame(1, ...)` ditambahkan pada tes produk ganda.
- Added: `tests/Feature/SmokeTest.php` (3 tes: seluruh halaman admin/pelanggan/publik dapat dirender, tema tidak tertukar, hero tanpa kartu ilustrasi). Jumlah tes sekarang 28.
- **Hero:** kartu ilustrasi dashboard (Jetson Orin Nano, status, progres, pembayaran) dihapus dari `home.blade.php` dan seluruh CSS `.pit-hero-card*` dihapus (bukan disembunyikan). Hero menjadi satu kolom rata tengah; tiga poin kepercayaan menjadi satu baris. Kelas `.pit-tile` (global) menggantikan pemakaian ubin ikon di halaman Asisten AI.
- **Tema admin:** Navy × Yellow (#0B1F3A, #07172B, #FACC15, #2563EB, #F4F6FA) pada `dashboard.css` bagian `.pit-theme-admin`; sidebar Dark Navy dengan menu aktif kuning, topbar putih, kartu statistik navy/biru/kuning, tabel, tombol, grafik, toast. Berlaku di semua halaman yang memakai `layouts.dashboard` (seluruh admin). Dashboard pelanggan dan halaman publik tidak berubah.
- **OpenSSL:** penyebab ada di konfigurasi PHP komputer, bukan proyek; diagnosis dan perbaikan di `docs/TROUBLESHOOTING.md`.
- Dokumentasi: README, DESIGN_SYSTEM, PROJECT_DOCUMENTATION, AGENTS diperbarui (termasuk catatan 5B yang sebelumnya belum tercatat).
- **Belum diuji oleh pengembang perubahan ini:** `php artisan test` tidak dapat dijalankan di lingkungan penulis (tanpa PHP). Mohon jalankan dan laporkan hasilnya.

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


### 2026-11-17 — Fase 5C-2 / operasional rental (implementasi pada source overlay)
- Added: migration rekonsiliasi non-destruktif untuk tabel inti 5C-1; migration waktu pickup/return, pemeriksaan, denda, dan antrean rental.
- Added: model RentalInspection, RentalFine, dan RentalWaitlistEntry.
- Changed: AdminController kini membaca data operasional dari database; pembayaran COD diverifikasi admin; pickup membutuhkan pembayaran lunas dan konfirmasi manual; return merekam timestamp dan melepas reservasi; inspeksi menyimpan kondisi/bukti privat dan memperbarui status unit; denda tidak menetapkan tarif otomatis.
- Added: QR scanner kamera berbasis BarcodeDetector dan input manual; payload QR diverifikasi dengan HMAC lalu dicocokkan ke token hash invoice database. Scan tidak mengubah status; tombol konfirmasi terpisah.
- Changed: halaman dashboard dan laporan memakai query transaksi nyata; antrean disimpan database; customer dashboard tidak lagi menampilkan data contoh.
- Changed: klik detail produk mewajibkan login dan login kembali ke URL tujuan.
- Added: light/dark mode yang menyimpan pilihan perangkat pada seluruh layout publik dan dashboard; identitas aksen admin dan pelanggan dipertahankan.
- Changed: DatabaseSeeder hanya membuat kategori taksonomi; tidak lagi menambahkan katalog produk atau unit contoh.
- Tests: ditambahkan skenario alur operasi admin (pembayaran, scanner, pickup, return, inspeksi, denda), masa berlaku scanner, perlindungan sesi dari QR salah, no-op seeder, dan akses detail produk. Helper tes yang bentrok dengan `TestCase::options()` diganti menjadi `adminArgs()`.
- Verification: belum dapat menjalankan Artisan/PHPUnit pada paket sumber ini karena arsip tidak menyertakan instalasi Laravel lengkap (`artisan`, `composer.json`, `bootstrap/`, dan `vendor/`). Pemeriksaan sintaks lokal dilakukan terpisah; integrasi harus diuji pada proyek Laravel penuh.
- Still pending: payment gateway/webhook QRIS/VA, notifikasi WA/email, tarif/denda baku dari mitra, integrasi penjualan Shopee, dan konfirmasi browser perangkat kamera.
