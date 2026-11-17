# Panduan Screenshot Bukti

Maksimal 1–3 gambar per kelompok. Pilih yang membuktikan fitur **berjalan**, bukan hanya tampilan. Simpan di folder `bukti/` (tidak di-commit) dengan nama yang disarankan.

## Minggu ke-4 (database, autentikasi, CRUD, SEO, pengujian)
| # | Buka | Harus terlihat | Membuktikan | Nama file | Bagian laporan |
|---|---|---|---|---|---|
| 1 | Terminal: `php artisan migrate:status` | Daftar migration berstatus Ran | Skema database terbentuk | `m4-01-migrasi.png` | Database |
| 2 | phpMyAdmin → database `pitlabs` → daftar tabel | Tabel users, categories, products, inventory_units | Database terintegrasi | `m4-02-tabel-database.png` | Database |
| 3 | `/daftar`: kirim formulir kosong atau kata sandi pendek | Pesan galat validasi di bawah kolom | Validasi input | `m4-03-validasi-registrasi.png` | Autentikasi |
| 4 | Login sebagai pelanggan lalu buka `/admin` | Halaman 403 dengan URL `/admin` terlihat | Otorisasi role | `m4-04-akses-admin-ditolak.png` | Keamanan |
| 5 | Login sebagai admin → Produk → Tambah/Ubah produk | Formulir produk terisi, dan daftar produk setelah disimpan | CRUD produk | `m4-05-crud-produk.png` | Fitur utama / CRUD |
| 6 | Admin → Inventaris → Tambah unit dengan kode yang sama dua kali | Pesan "Kode unit ini sudah dipakai" | CRUD unit dan validasi unik | `m4-06-unit-kode-unik.png` | CRUD |
| 7 | `/produk/<slug>` → klik kanan → Lihat sumber halaman | Tag `<title>`, `meta description`, `canonical`, JSON-LD | SEO | `m4-07-seo-head.png` | SEO |
| 8 | `/sitemap.xml` | Daftar URL produk | SEO | `m4-08-sitemap.png` | SEO |
| 9 | Terminal: `php artisan test` | Ringkasan tes lolos | Pengujian | `m4-09-hasil-tes.png` | Pengujian |
| 10 | GitHub → halaman Commits | Daftar commit beberapa anggota | Kontribusi tim, Git | `m4-10-commit-github.png` | Kerja sama tim |

Foto produk (bila diunggah): satu gambar halaman detail produk dengan foto, `m4-11-foto-produk.png`, bagian CRUD.

## Minggu ke-5 (diisi setelah fitur selesai)
Scan invoice, checkout dan invoice, verifikasi pembayaran, pengambilan dan pengembalian, light/dark mode: panduan ditambahkan saat fiturnya selesai dan diuji.
