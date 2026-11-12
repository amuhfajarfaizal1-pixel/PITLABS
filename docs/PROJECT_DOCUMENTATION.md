# PITLABS — Dokumentasi Proyek

Pedoman untuk developer dan AI agent. Baca bersama [../README.md](../README.md), [../AGENTS.md](../AGENTS.md), dan [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md).

## A. Gambaran proyek
- **Apa:** platform web penyewaan perangkat AI, IoT, dan teknologi. Dua jalur: **sewa** (diproses internal) dan **beli via Shopee** (diselesaikan di Shopee).
- **Masalah:** pemesanan manual lewat chat lambat dan rawan keliru; ketersediaan, pembayaran, pengambilan, dan denda tidak tercatat rapi.
- **Pengguna:** pelanggan (mahasiswa, peneliti, pembuat proyek) dan admin mitra.
- **Alur sewa:** pilih produk → cek ketersediaan → login → pilih unit dan metode (QRIS/VA/COD) → setujui ketentuan → pesanan dan invoice (QR) terbit → bayar → ambil di lokasi admin (scan QR) → kembalikan → admin memeriksa kondisi → selesai atau denda.

## B. Riwayat pengembangan
- **Awal:** prototipe statis HTML/CSS lalu prototipe **PHP native + MySQL (XAMPP)**: router `?page=`, 7 tabel, data produk dari database. Desain dasar (hero navy, kartu produk, tab admin) berasal dari sini.
- **Pindah ke Laravel:** ketentuan PjBL mewajibkan Bootstrap + framework (Laravel/CodeIgniter); URL bersih untuk SEO; struktur Blade/komponen lebih mudah dirawat dan diuji. Stack final: Laravel + Blade + Bootstrap 5.
- **Fase 1** analisis referensi dan keputusan stack · **Fase 2** design system · **Fase 3** homepage · **Fase 4** katalog, detail, ketersediaan, auth (UI), dashboard pelanggan dan admin, checkout, invoice, halaman informasi, antrean rental.
- **Perbaikan UI (Fase 5):** kartu ilustrasi di hero homepage dihapus (hero satu kolom); tema admin diganti Navy × Yellow dan diterapkan ke seluruh halaman admin; tes diperbaiki (isolasi database, fixture unik).
- **Fase 5A** database (MySQL) + autentikasi sungguhan: migration, model, seeder, Laravel Auth, role, throttle, profil, ganti kata sandi, daftar pelanggan admin, tes autentikasi.
- **Belum:** Fase 5B dst. dan Fase 6–7. Kronologi rinci dan file terdampak: [DEVELOPMENT_LOG.md](DEVELOPMENT_LOG.md).

## C. Stack dan fungsi
| Teknologi | Fungsi |
|---|---|
| Laravel (PHP 8.2+) | Routing, controller, Blade, validasi; nanti Eloquent, Auth, Socialite |
| Blade | Template dan komponen (`<x-...>`) |
| Bootstrap 5.3.3 (CDN) | Grid, utilitas, komponen dasar (modal, dropdown, offcanvas, accordion) |
| `pitlabs.css` / `dashboard.css` | Token desain, override Bootstrap, komponen PITLABS |
| JavaScript murni | Validasi form klien, filter/cari tabel, aksi simulasi |
| Composer | Dependensi PHP |

## D. Arsitektur proyek
- **Route:** `routes/web.php`; publik, pelanggan (`auth` + `EnsureRole:customer`), admin (`auth` + `EnsureRole:admin`, prefix `/admin`).
- **Controller:** tipis, hanya menyiapkan data lalu memanggil view.
- **Data contoh (dipisah dari UI):** `app/Support/MockCatalog` (7 produk), `MockAvailability` (jadwal booking contoh + aturan bentrok), `MockData` (pesanan, pelanggan, antrean, KPI, grafik). View hanya menerima array; mengganti ke database tidak perlu mengubah UI.
- **Layout:** `layouts/app` (situs publik) dan `layouts/dashboard` (admin dan pelanggan; tema dipilih dari URL).
- **Komponen:** `x-field`, `x-modal`, `x-alert`, `x-status-badge`, `x-empty-state`, `x-pending`, `x-breadcrumb`, `x-product-card`, `x-terms-checkbox`; dashboard: `x-dash.page-header|stat|panel|table|bars`.
- **Konfigurasi:** `config/pitlabs.php` memegang identitas, navigasi, dan seluruh label status.
- **CSS/JS:** token di `:root` pada `pitlabs.css`; gaya halaman di `public/assets/css/pages/`; dashboard di `dashboard.css`. Prefix kelas: `pit-` (situs), `dash-` (dashboard).
- **Penanda data belum ada:** `<x-pending field="...">`. Cari: `grep -r "data-needs-data" resources/views`.
- **Frontend ↔ backend:** frontend memanggil controller; Fase 5 mengganti `Mock*` bertahap (autentikasi sudah diganti di 5A). Jangan membuat logika bisnis di view.

## E. Design system
Rincian token, komponen, dan aturan: [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md). Ringkas:
- **Brand:** Navy #0A2540, Sapphire #1463FF, Cyan #00A3FF, Turquoise #00F5D4, Light Gray #E9ECEF. **Oranye hanya tombol Beli via Shopee.**
- **Logo:** `public/assets/img/logo-pitlabs.png`; huruf "IT" berwarna navy, jadi di latar navy harus di atas panel putih.
- **Dashboard pelanggan:** identitas brand (Sapphire). **Dashboard admin:** Navy #0B1F3A / #07172B × Yellow #FACC15 × Blue #2563EB × netral #F4F6FA; teks di atas kuning selalu navy. Keduanya berbagi komponen; hanya variabel `--dash-*` yang berbeda.
- **Status:** order, pembayaran, pengambilan, penyewaan **selalu terpisah**; badge = ikon + teks.

## F. Sistem
**Hak akses:** guest (katalog, cek ketersediaan), customer (pesan, dashboard), admin (operasional, laporan). Ditegakkan di server oleh middleware `auth` + `EnsureRole`.

| Kategori | Item |
|---|---|
| Diimplementasikan (frontend) | Semua halaman pada README, validasi form klien, filter/cari tabel, modal konfirmasi, cetak invoice, SEO dasar (title/meta, canonical, sitemap, robots, JSON-LD) |
| Diimplementasikan (backend, 5A) | Registrasi, login/logout, hash kata sandi, role, akun nonaktif, throttle login, profil, ganti kata sandi, aktif/nonaktifkan pelanggan, seeder 7 produk dan unit contoh, tes autentikasi |
| Simulasi (tampil, tidak tersimpan) | Tombol Google login, aksi tabel admin selain pelanggan, checkout, antrean, QR invoice, data dashboard, katalog (masih `MockCatalog`) |
| Perlu backend (5B dst.) | Google OAuth, katalog dari DB dan CRUD produk/unit, pesanan, pencegahan bentrok di server, payment gateway + webhook, scan QR kamera, laporan nyata, audit trail, pengingat WA/email |
| Belum disepakati / data menunggu mitra | Tarif sewa dan jual, nominal denda, deposit, durasi min/maks, alamat dan jam pengambilan, URL Shopee, pilihan payment gateway, kebijakan refund dan pembatalan, foto dan spesifikasi produk |

Aturan bisnis resmi: BUSINESS_RULES.md pada Documentation Pack (status terpisah, COD, bentrok periode `new_start < existing_end AND new_end > existing_start`, inspeksi sebelum denda).

## F2. Basis data (5A)
Tabel yang sudah ada: `users`, `categories`, `products`, `inventory_units` (+ tabel bawaan Laravel: `sessions`, `cache`, `jobs`, `password_reset_tokens`). Mengikuti [schema.sql pada Documentation Pack], dengan dua penyesuaian: kolom kata sandi bernama `password` (konvensi Laravel, bukan `password_hash`) dan nullable (akun Google); `users.google_id` ditambahkan untuk 5B. Role: `CUSTOMER` | `ADMIN`. Tabel pesanan, reservasi, pembayaran, invoice, serah-terima, pemeriksaan, denda, dan audit dibuat pada batch berikutnya. Seeder bawaan: 2 kategori, 7 produk (tanpa harga/spesifikasi), 15 unit contoh. **Akun admin dibuat manual** (`php artisan pitlabs:create-admin` atau `AdminSeeder` yang membaca `.env`); tidak ada akun yang dibuat otomatis.

## G. Catatan perkembangan
Lihat [DEVELOPMENT_LOG.md](DEVELOPMENT_LOG.md) (kronologis, dengan file terdampak). Tanggal hanya dicatat jika diketahui; belum ada riwayat commit yang dapat diverifikasi dari dokumentasi ini.
