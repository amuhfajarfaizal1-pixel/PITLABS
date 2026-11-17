# PITLABS — Dokumentasi Proyek

Pedoman untuk developer dan AI agent. Baca bersama [../README.md](../README.md), [../AGENTS.md](../AGENTS.md), dan [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md).

## A. Gambaran proyek
- **Apa:** platform web penyewaan perangkat AI, IoT, dan teknologi. Dua jalur: **sewa** (diproses internal) dan **beli via Shopee** (diselesaikan di Shopee).
- **Masalah:** pemesanan manual lewat chat lambat dan rawan keliru; ketersediaan, pembayaran, pengambilan, dan denda tidak tercatat rapi.
- **Pengguna:** pelanggan (mahasiswa, peneliti, pembuat proyek) dan admin mitra.
- **Alur sewa pada source saat ini:** katalog → login sebelum detail/checkout → cek ketersediaan dan pilih unit → setujui ketentuan → pesanan dan invoice ber-QR terbit → pembayaran COD dikonfirmasi admin → scanner memvalidasi invoice → admin mengonfirmasi serah-terima → pelanggan mengembalikan → admin memeriksa kondisi → selesai atau catatan denda manual. QRIS/VA belum aktif.

## B. Riwayat pengembangan
- **Awal:** prototipe statis HTML/CSS lalu prototipe **PHP native + MySQL (XAMPP)**: router `?page=`, 7 tabel, data produk dari database. Desain dasar (hero navy, kartu produk, tab admin) berasal dari sini.
- **Pindah ke Laravel:** ketentuan PjBL mewajibkan Bootstrap + framework (Laravel/CodeIgniter); URL bersih untuk SEO; struktur Blade/komponen lebih mudah dirawat dan diuji. Stack final: Laravel + Blade + Bootstrap 5.
- **Fase 1** analisis referensi dan keputusan stack · **Fase 2** design system · **Fase 3** homepage · **Fase 4** katalog, detail, ketersediaan, auth (UI), dashboard pelanggan dan admin, checkout, invoice, halaman informasi, antrean rental.
- **Perbaikan UI (Fase 5):** kartu ilustrasi di hero homepage dihapus (hero satu kolom); tema admin diganti Navy × Yellow dan diterapkan ke seluruh halaman admin; tes diperbaiki (isolasi database, fixture unik).
- **Fase 5A** database (MySQL) + autentikasi sungguhan: migration, model, seeder, Laravel Auth, role, throttle, profil, ganti kata sandi, daftar pelanggan admin, tes autentikasi.
- **Status source terbaru:** 5A–5B dan 5C-1 telah tersedia; implementasi 5C-2 (pembayaran COD, QR scanner, pengambilan, pengembalian, inspeksi, denda, antrean), 5E (dashboard/laporan DB), dan tema light/dark telah ditambahkan pada source 17 November 2026. Semua perubahan 5C-2 masih menunggu uji integrasi pada instalasi Laravel lengkap. Kronologi: [DEVELOPMENT_LOG.md](DEVELOPMENT_LOG.md).

## C. Stack dan fungsi
| Teknologi | Fungsi |
|---|---|
| Laravel (PHP 8.2+) | Routing, controller, Blade, validasi; nanti Eloquent, Auth, Socialite |
| Blade | Template dan komponen (`<x-...>`) |
| Bootstrap 5.3.3 (CDN) | Grid, utilitas, komponen dasar (modal, dropdown, offcanvas, accordion) |
| `pitlabs.css` / `dashboard.css` | Token desain, override Bootstrap, komponen PITLABS |
| JavaScript murni | Tema, pemindai QR melalui `BarcodeDetector` bila didukung browser, input manual fallback, filter/cari tabel |
| Composer | Dependensi PHP |

## D. Arsitektur proyek
- **Route:** `routes/web.php`; publik, pelanggan (`auth` + `EnsureRole:customer`), admin (`auth` + `EnsureRole:admin`, prefix `/admin`).
- **Controller:** mengelola validasi, otorisasi dan orkestrasi alur; transaksi dan perubahan status penting memakai database transaction serta audit log.
- **Sumber data operasional:** katalog, inventaris, pesanan, reservasi, invoice, pembayaran, inspeksi, denda, antrean, KPI dan laporan pada halaman yang dialihkan ke implementasi terbaru dibaca dari database. Kelas `Mock*` lama boleh tersisa sebagai kode legacy untuk bagian yang belum dipensiunkan, tetapi tidak boleh dipakai untuk mengisi transaksi/dashboard aktual.
- **Layout:** `layouts/app` (situs publik) dan `layouts/dashboard` (admin dan pelanggan); pilihan tema terang/gelap disimpan di browser, admin tetap Navy × Yellow dan pelanggan Sapphire.
- **Komponen:** `x-field`, `x-modal`, `x-alert`, `x-status-badge`, `x-empty-state`, `x-pending`, `x-breadcrumb`, `x-product-card`, `x-terms-checkbox`; dashboard: `x-dash.page-header|stat|panel|table|bars`.
- **Konfigurasi:** `config/pitlabs.php` memegang identitas, navigasi, dan seluruh label status.
- **CSS/JS:** token di `:root` pada `pitlabs.css`; gaya halaman di `public/assets/css/pages/`; dashboard di `dashboard.css`. Prefix kelas: `pit-` (situs), `dash-` (dashboard).
- **Penanda data belum ada:** `<x-pending field="...">`. Cari: `grep -r "data-needs-data" resources/views`.
- **Frontend ↔ backend:** frontend memanggil route/controller dan service; alur bisnis tidak diletakkan di view. Detail produk memerlukan autentikasi; setelah login, pengguna dikembalikan ke halaman detail yang diminta.

## E. Design system
Rincian token, komponen, dan aturan: [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md). Ringkas:
- **Brand:** Navy #0A2540, Sapphire #1463FF, Cyan #00A3FF, Turquoise #00F5D4, Light Gray #E9ECEF. **Oranye hanya tombol Beli via Shopee.**
- **Logo:** `public/assets/img/logo-pitlabs.png`; huruf "IT" berwarna navy, jadi di latar navy harus di atas panel putih.
- **Dashboard pelanggan:** identitas brand (Sapphire). **Dashboard admin:** Navy #0B1F3A / #07172B × Yellow #FACC15 × Blue #2563EB × netral #F4F6FA; teks di atas kuning selalu navy. Keduanya berbagi komponen; hanya variabel `--dash-*` yang berbeda.
- **Status:** order, pembayaran, pengambilan, penyewaan **selalu terpisah**; badge = ikon + teks.

## F. Sistem

**Hak akses:** guest dapat melihat homepage, katalog dan cek ketersediaan; detail produk memerlukan login. Customer dapat checkout, melihat invoice/pesanan/antrean/profil. Admin dapat mengelola katalog dan menjalankan operasi rental. Otorisasi ditegakkan di server oleh middleware `auth` + `EnsureRole` dan pemeriksaan kepemilikan invoice.

| Area | Source tersedia | Status verifikasi |
|---|---|---|
| Registrasi/login/logout, role, profil, akun pelanggan | Diimplementasikan | Tes dari batch terdahulu perlu regresi setelah patch |
| Katalog, CRUD produk/unit, upload foto, SEO | Diimplementasikan | Perlu tes regresi |
| Booking/reservasi, pencegahan bentrok, checkout COD, invoice QR | Diimplementasikan | Belum teruji end-to-end pada source terbaru |
| Verifikasi pembayaran COD | Diimplementasikan; jumlah diterima direkam hanya sesuai uang yang benar-benar diterima dan keputusan mitra | Belum diuji pada instalasi penuh |
| QR scanner | Payload QR HMAC + hash invoice DB; manual invoice number sebagai fallback; scan tidak mengubah status | Uji kamera lintas-browser belum dilakukan |
| Pengambilan/pengembalian/inspeksi | Diimplementasikan dengan status terpisah, audit dan bukti pemeriksaan privat | Belum diuji pada alur fisik |
| Denda | Catatan tanpa nominal; admin mengisi nominal berdasarkan kebijakan yang disahkan | Kebijakan mitra belum tersedia |
| Antrean rental | Data DB dan transisi WAITING/NOTIFIED/FULFILLED/CANCELLED | Notifikasi eksternal tidak dikirim otomatis |
| Dashboard/laporan | Query pembayaran terverifikasi, denda paid, inventaris dan pesanan DB | Belum direkonsiliasi dengan data operasional |
| Light/dark mode | Layout publik dan dashboard | Perlu uji visual dan perangkat |

### Pembayaran dan tarif
- Hanya COD dapat diverifikasi melalui admin. QRIS/Virtual Account memerlukan provider, kredensial dan webhook; belum diaktifkan.
- Jika total belum ditetapkan, aplikasi tidak membuat tarif. Admin mencatat uang COD yang benar-benar diterima sesuai persetujuan mitra.
- Denda disimpan `PENDING_REVIEW` tanpa nominal sampai admin membuat keputusan sesuai kebijakan mitra. Tidak ada rumus tarif yang dikarang.
- Transaksi pembelian melalui Shopee berada di luar PITLABS dan tidak dihitung pada laporan rental.

### Status dan integritas
- Status pesanan, pembayaran, pengambilan, dan penyewaan selalu terpisah.
- Scan QR/manual hanya menampilkan data invoice hasil validasi. Perubahan pickup memerlukan aksi konfirmasi admin yang terpisah.
- Produk, unit, dan pesanan tidak dihapus permanen; gunakan nonaktif/pensiun agar histori dan foreign key terjaga.
- Seeder bawaan hanya menambahkan kategori taksonomi dasar; `ProductSeeder`, `InventoryUnitSeeder`, dan `UserSeeder` lama dibuat no-op. Produk, unit, pelanggan dan transaksi nyata disediakan melalui data mitra dan aktivitas pengguna.

### Skema transaksi
Tabel rental inti: `settings`, `rental_orders`, `rental_order_items`, `reservations`, `invoices`, `payments`, `audit_logs`. Tabel operasional tambahan: `rental_inspections`, `rental_fines`, `rental_waitlist_entries`. Migration rekonsiliasi 17 November dirancang membuat hanya tabel yang belum ada dan tidak menghapus tabel/data yang sudah tersedia.

**Catatan pengujian:** source archive yang diterima tidak memuat `artisan`, `composer.json`, `composer.lock`, `bootstrap/`, `.env`, atau `vendor/`; karena itu uji migration, kompilasi Blade dan PHPUnit perlu dijalankan pada instalasi Laravel lengkap.


## G. Catatan perkembangan
Lihat [DEVELOPMENT_LOG.md](DEVELOPMENT_LOG.md) (kronologis, dengan file terdampak). Tanggal hanya dicatat jika diketahui; belum ada riwayat commit yang dapat diverifikasi dari dokumentasi ini.
