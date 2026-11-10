# PITLABS

**Your Premium IoT & AI Technology Hub** — platform web penyewaan perangkat AI, IoT, dan teknologi.
Proyek PjBL kelompok **FAR STAR**, mata kuliah Web Design and Programming, D4 Teknik Komputer dan Jaringan, Politeknik Negeri Ujung Pandang. Mitra: PT. Pulau Inovasi Teknologi.

> Status: **frontend selesai sampai Fase 4** (tampilan + simulasi). Backend, database, dan integrasi nyata belum dikerjakan. Lihat [status pengembangan](#status-pengembangan).

## Latar belakang dan tujuan
Pemesanan perangkat lewat chat manual lambat dan rawan keliru. PITLABS merapikan satu alur: **pilih → cek ketersediaan → pesan dan bayar → ambil di lokasi admin → kembalikan → diperiksa**. Pembelian dilakukan di Shopee dari halaman detail produk; PITLABS tidak memproses pembayaran jual.

## Katalog resmi (7 produk)
Perangkat fisik: Laptop, Raspberry Pi 5, Jetson Orin Nano, Drone, Kamera Mirrorless. Layanan digital: Google Colab Pro, Google Colab Pro Plus. Tidak boleh menambah produk tanpa persetujuan.

## Fitur
| Area | Fitur | Status |
|---|---|---|
| Publik | Homepage, katalog (cari + filter), detail produk, cek ketersediaan, FAQ, Panduan, Tentang, Artikel, Asisten AI (Segera Hadir) | Frontend ✅ |
| Autentikasi | Login, registrasi, login Google, logout | UI + validasi ✅, **simulasi** |
| Pelanggan | Dashboard, penyewaan aktif, riwayat, antrean rental, informasi pengembalian, profil, checkout, invoice | Frontend ✅, data contoh |
| Admin | Dashboard, New Orders, Ongoing Rentals, verifikasi pembayaran, QR Scanner, verifikasi pengambilan, pengembalian, pemeriksaan kondisi, denda, produk, inventaris, pelanggan, antrean rental, laporan keuangan | Frontend ✅, data contoh |
| Backend | Database, auth sungguhan, CRUD, payment gateway, webhook, Google OAuth | ⬜ Fase 5 |

## Teknologi
Laravel · PHP 8.2+ · Blade · Bootstrap 5.3.3 · Bootstrap Icons 1.11.3 · JavaScript dan CSS murni · Composer. Font: Sora, Plus Jakarta Sans, JetBrains Mono (Google Fonts). Database target: MySQL (rancangan di [database/schema.sql](docs/) pada Documentation Pack); belum dikonfigurasi di frontend.

## Persyaratan
PHP ≥ 8.2 (dengan ekstensi `mbstring`, `openssl`, `fileinfo`, `curl`, `zip`), Composer, koneksi internet (CDN Bootstrap dan font). Catatan: XAMPP dengan PHP 8.0 **tidak cukup**.

## Instalasi
```bash
composer create-project laravel/laravel pitlabs
cd pitlabs
# salin isi paket PITLABS ke root project (timpa file yang sama)
cp .env.example .env            # jika belum ada
php artisan key:generate        # jika APP_KEY kosong
```
Di `.env`: `APP_ENV=local`, `SESSION_DRIVER=file` (database belum dipakai).

## Menjalankan
```bash
php artisan view:clear && php artisan route:clear
php artisan serve
```
Buka `http://127.0.0.1:8000/masuk` dan pilih **Admin demo** atau **Pelanggan demo**. Halaman referensi komponen: `/design-system` (hanya `APP_ENV=local`).

## Struktur direktori utama
```
app/Http/Controllers/   Home, Catalog, Availability, Auth, Customer, Admin, Page, Seo
app/Http/Middleware/    DemoRole (penjaga rute simulasi)
app/Support/            Pit (helper), DemoAuth (auth simulasi), MockCatalog, MockAvailability, MockData
config/pitlabs.php      identitas, navigasi, navigasi dashboard, label status
resources/views/        layouts/, partials/, components/ (+ dash/), pages/, catalog/, auth/, admin/, dashboard/
public/assets/css/      pitlabs.css (token + komponen), dashboard.css, pages/*.css
public/assets/js/       pitlabs.js (form), dashboard.js (tabel, aksi simulasi)
docs/                   dokumentasi proyek
```

## Arsitektur singkat
Browser → route Laravel → controller tipis → **lapisan data contoh** (`app/Support/Mock*`) → view Blade → komponen + CSS/JS. Fase 5 mengganti lapisan data contoh dengan model/database dan `DemoAuth` dengan Laravel Auth. Detail: [PROJECT_DOCUMENTATION.md](docs/PROJECT_DOCUMENTATION.md).

## Status pengembangan
Fase 1 analisis ✅ · 2 design system ✅ · 3 homepage ✅ · 4 halaman customer, admin, dan publik ✅ (belum diuji di browser) · 5 backend dan database ⬜ · 6 pengujian dan keamanan ⬜ · 7 dokumentasi akhir dan demo ⬜. Rincian: [DEVELOPMENT_LOG.md](docs/DEVELOPMENT_LOG.md), [PJBL_CHECKLIST.md](docs/PJBL_CHECKLIST.md).

## Kontribusi
Baca [AGENTS.md](AGENTS.md) sebelum mengubah apa pun. Gunakan cabang per fitur, commit dengan akun masing-masing (kontribusi tiap anggota dinilai), dan perbarui dokumentasi bila perubahan memengaruhi sistem.

## Dokumentasi
[PROJECT_DOCUMENTATION.md](docs/PROJECT_DOCUMENTATION.md) · [DESIGN_SYSTEM.md](docs/DESIGN_SYSTEM.md) · [PJBL_CHECKLIST.md](docs/PJBL_CHECKLIST.md) · [DATA_PRODUK_YANG_DIBUTUHKAN.md](docs/DATA_PRODUK_YANG_DIBUTUHKAN.md) · [DEVELOPMENT_LOG.md](docs/DEVELOPMENT_LOG.md)
