# Catatan Logbook PITLABS

Bahan untuk mengisi spreadsheet logbook PjBL. Disusun dari bukti yang ada; bagian yang tidak dapat dipastikan ditandai **[belum terverifikasi]**. Pekerjaan yang dilakukan pemilik di luar percakapan (mis. menjalankan tes, push ke GitHub) hanya dicatat jika pemilik melaporkannya.

## Cara membaca tanggal
- Tanda waktu berkas dan log teknis di sandbox memakai **UTC**. Logbook memakai **WITA (UTC+8)**. Kecocokan zona waktu terverifikasi: foto unggahan pemilik bernama `20261109_233445…` (23.34 lokal) bertanda waktu 15.35 UTC.
- Konversi dipakai pada tabel di bawah. Contoh: 22.41 UTC tanggal 9 Nov = 06.41 WITA tanggal 10 Nov.
- `DEVELOPMENT_LOG.md` masih berlabel tanggal UTC; gunakan tabel ini untuk logbook.

## Target logbook (dari screenshot spreadsheet)
| Minggu | Checkpoint | Terbaca di screenshot |
|---|---|---|
| 1 | Penentuan ide, observasi lapangan, wawancara, diskusi bersama mitra | Terisi (24 dan 27 Okt 2026) |
| 2 | Desain UI/UX aplikasi website | Terisi (27 Okt, 30 Okt, 1 Nov 2026) |
| 3 | Desain UI/UX aplikasi website | Terisi (3 dan 4–8 Nov 2026) |
| 4 | Pembuatan fitur utama, implementasi CRUD, database (pastikan SEO friendly) | Kosong |
| 5 | Pembuatan fitur utama, implementasi CRUD, database (pastikan SEO friendly) | Kosong |
| 6 | Pembuatan fitur utama, integrasi pihak ketiga (authentication, payment gateway, dll), pengujian sistem, error handling, perbaikan bug | Kosong |

**Belum terverifikasi (mohon konfirmasi):**
1. Kolom nomor minggu dan tanggal tidak terlihat di screenshot. Pemetaan "Minggu 4 = 9–15 Nov 2026" dan "Minggu 5 = 16–22 Nov 2026" **telah dikonfirmasi pemilik** (setelah 12 Nov 2026, 22.09 WITA).
2. Baris minggu 2 tertulis "**Kamis**, 27 Oktober 2026". 27 Okt 2026 jatuh pada **Selasa**; Kamis adalah 29 Okt. Mohon periksa apakah itu salah ketik sebelum laporan dikumpulkan.
3. Sebagian teks pada kolom Kegiatan terpotong di sisi kanan screenshot (nama mitra, tautan GitHub).

---

## MINGGU KE-4 (9–15 November 2026) — pencapaian sampai Kamis, 12 November 2026
Target: pembuatan fitur utama, implementasi CRUD, database, SEO friendly.

```
Hari/Tanggal: Senin, 9 November 2026
Kegiatan: Mempelajari dokumen acuan proyek: Documentation Pack (PRD, aturan bisnis, rancangan database, kontrak API), panduan PjBL dari dosen, dan prototipe PHP native; menetapkan stack Laravel + Bootstrap 5 dan rencana pengembangan bertahap.
Hasil: Ringkasan pemahaman sistem, daftar kebutuhan PjBL, dan rencana struktur frontend.
Keterangan: Waktu unggah berkas acuan 18.26–18.35 WITA (terverifikasi dari tanda waktu berkas). Lama pengerjaan [belum terverifikasi].
```
```
Hari/Tanggal: Senin–Selasa, 9–10 November 2026
Kegiatan: Membangun frontend: design system (warna, tipografi, komponen), homepage, katalog, detail produk, cek ketersediaan, login dan registrasi, FAQ, panduan, tentang kami, artikel, dashboard admin dan pelanggan, checkout, dan invoice dengan data contoh.
Hasil: Paket frontend Fase 2–4 (tanda waktu paket: Selasa 10 Nov 06.41 WITA, 17.21 WITA, dan 17.25 WITA). Pemilik menyatakan telah melihat dan menyetujui tahap desain.
Keterangan: Diimplementasikan; masih data contoh. Pemeriksaan visual dilakukan pemilik, tanggal persetujuan [belum terverifikasi]. Pada 10 Nov (17.18 WITA) pemilik mengirim prototipe final untuk dicocokkan; hasilnya: halaman AI dibuat "Segera Hadir" dan halaman Antrean serta Pelanggan ditambahkan.
```
```
Hari/Tanggal: Rabu, 11 November 2026
Kegiatan: Memulai backend. Fase 5A: migration dan model database (users, categories, products, inventory_units), seeder, autentikasi Laravel (registrasi, login, logout), role Customer/Admin, middleware otorisasi, profil dan ganti kata sandi. Fase 5B: katalog dibaca dari database, CRUD produk dan unit oleh admin, pencarian dan filter.
Hasil: Paket 5A (05.27 WITA) dan 5B (10.15 WITA); 25 tes otomatis ditulis. Pemilik menjalankan tes: 22 lolos, 3 gagal.
Keterangan: Tiga kegagalan disebabkan fixture tes yang membuat email ganda dan ExampleTest tanpa isolasi database, bukan cacat fitur. Jam pemilik menjalankan tes [belum terverifikasi].
```
```
Hari/Tanggal: Rabu malam, 11 November 2026
Kegiatan: Memperbaiki tiga tes yang gagal (isolasi database dengan RefreshDatabase, fixture unik); menghapus kartu ilustrasi di hero homepage; menerapkan tema admin Navy × Yellow; menyusun skrip pembaruan dan alur Git ke GitHub; mendiagnosis peringatan OpenSSL ganda.
Hasil: Paket perbaikan (20.40 WITA) dan paket alur Git (20.48 WITA).
Keterangan: Hasil tes setelah perbaikan belum dilaporkan. Peringatan OpenSSL berasal dari konfigurasi PHP di komputer pemilik dan belum dipastikan teratasi. Penyebab kegagalan push GitHub belum diketahui (menunggu pesan galat).
```
```
Hari/Tanggal: Kamis, 12 November 2026
Kegiatan: Mengubah pembuatan akun admin menjadi manual (perintah Artisan pitlabs:create-admin); menambah unggah foto produk beserta teks alternatif dan perbaikan gaya kartu produk di katalog; memperbaiki galat tes (nama fungsi bantu bentrok dengan TestCase) dan mendiagnosis APP_KEY yang hilang.
Hasil: Paket admin manual (06.15 WITA), foto produk (11.56 WITA), perbaikan tes (17.15 WITA).
Keterangan: Diimplementasikan, belum diuji ulang oleh pemilik. Pemilik menerima aturan kerja baru dan target logbook (22.09 WITA).
```

```
Hari/Tanggal: Jumat–Minggu, 13–15 November 2026
Kegiatan: Setelah pemilik memutuskan parameter sewa dikosongkan dulu (admin mengisi kemudian), scan invoice memakai QR terverifikasi dan kamera, serta tema mengikuti perangkat, dikerjakan Fase 5C-1: tabel pesanan (rental_orders, rental_order_items, reservations, invoices, payments, audit_logs, settings), booking dengan pencegahan bentrok jadwal dalam satu transaksi database, checkout COD, invoice bernomor unik dengan identitas QR (HMAC, hash disimpan), ketersediaan dibaca dari reservasi database, dashboard pelanggan dari data pesanan, dan halaman Pengaturan admin (durasi min/maks, alamat dan jam pengambilan). Dibuat 3 berkas tes baru (booking, pengaturan, token invoice).
Hasil: 7 migration, 7 model, layanan BookingService/Availability/InvoiceToken/InvoiceQr, perubahan checkout dan invoice, serta tes. Pemeriksaan statis dilakukan (sintaks Blade, nama route, kurung).
Keterangan: Jejak berkas: migration dibuat Jumat 13 Nov 17.19 WITA; BookingService Sabtu 14 Nov 06.55 WITA; tes dan pemeriksaan terakhir Minggu 15 Nov 18.22 WITA. Status: sudah diimplementasikan, BELUM diuji (php artisan test belum dijalankan oleh pemilik; sandbox pengembang tidak memiliki PHP). QR membutuhkan paket bacon/bacon-qr-code yang belum dipasang. Halaman admin untuk pesanan, pembayaran, pengambilan, dan scan masih data contoh (5C-2).
```

### Status pencapaian target minggu ke-4 (per Minggu, 15 Nov 2026)
| Bagian target | Status |
|---|---|
| Database (4 tabel + migration + seeder) | Sudah diimplementasikan; sebagian sudah diuji (22 tes lolos yang dilaporkan pemilik) |
| Autentikasi dan role | Sudah diimplementasikan; sudah diuji (tes autentikasi termasuk yang lolos) |
| CRUD produk dan unit | Sudah diimplementasikan; sudah diuji (tes katalog dan CRUD termasuk yang lolos) |
| Unggah foto produk | Sudah diimplementasikan; belum diuji |
| SEO friendly (URL bersih, meta, sitemap, robots, data terstruktur) | Sudah diimplementasikan; belum diaudit (Lighthouse/validator belum dijalankan) |
| Booking, checkout COD, invoice dengan QR, ketersediaan dari database, pengaturan admin | Sudah diimplementasikan (5C-1); **belum diuji** |
| Verifikasi pembayaran, pengambilan, pengembalian, pemeriksaan, denda (admin) | Belum dikerjakan di backend (tampilan data contoh); 5C-2 |
| Scan invoice dengan kamera | Belum dikerjakan (5C-2); fondasi QR dan token sudah ada |
| Light dan dark mode | Belum dikerjakan |

**Dipercepat dari jadwal:** pengujian otomatis (target minggu ke-6) sudah dimulai pada minggu ke-4 (tes autentikasi, katalog, dan CRUD), termasuk validasi input dan otorisasi.

---

## MINGGU KE-5 (16–22 November 2026) — struktur, belum diisi
Target: pembuatan fitur utama, implementasi CRUD, database, SEO friendly.

Rencana kegiatan (menunggu keputusan pemilik pada daftar di akhir dokumen ini):
1. Booking dan pesanan di database, pencegahan bentrok jadwal.
2. Checkout dan invoice bernomor unik dengan QR terverifikasi.
3. Scan invoice di dashboard admin.
4. Verifikasi pembayaran, pengambilan, pengembalian, pemeriksaan, dan denda.
5. Dashboard dan laporan dari data sebenarnya.
6. Light mode dan dark mode.

```
Hari/Tanggal: [diisi setelah dikerjakan]
Kegiatan:
Hasil:
Keterangan:
```

Status pencapaian dan kendala: **[diisi setelah pekerjaan dilakukan]**.

## Keputusan pemilik yang masih ditunggu
Lihat bagian "Keputusan" di akhir pesan pengembangan terakhir dan `docs/DEVELOPMENT_LOG.md`.
