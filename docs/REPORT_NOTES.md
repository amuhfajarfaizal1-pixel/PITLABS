# Bahan Laporan PjBL PITLABS

Bahan mentah untuk dua laporan mingguan dan laporan akhir. Isi hanya fakta yang tercatat; angka tanggal mengikuti `LOGBOOK_NOTES.md` (WITA).

## Laporan minggu ini (Minggu ke-4, 9–15 Nov 2026, status per 12 Nov)
1. **Target:** fitur utama, CRUD, database, SEO friendly.
2. **Kegiatan:** lihat entri 9–12 Nov pada `LOGBOOK_NOTES.md`.
3. **Proses pengembangan:** analisis acuan → design system dan frontend dengan data contoh → backend bertahap: database (migration, model, seeder) → autentikasi dan role → katalog dari database → CRUD produk/unit → unggah foto → pengujian otomatis. Setiap batch diperiksa pemilik sebelum batch berikutnya.
4. **Hasil:**

| Komponen | Isi |
|---|---|
| Teknologi | Laravel 13, PHP 8.5, Blade, Bootstrap 5.3, MySQL (XAMPP), PHPUnit |
| Tabel database | users (role CUSTOMER/ADMIN), categories, products, inventory_units |
| Fitur terimplementasi | Registrasi, login, logout, profil, ganti kata sandi; katalog dan detail dari database; CRUD produk dan unit; kelola akun pelanggan; unggah foto produk; sitemap, robots, meta, data terstruktur |
| Fitur masih data contoh | Booking, checkout, invoice, pembayaran, pengambilan, pengembalian, pemeriksaan, denda, dashboard, laporan, antrean |
| Keamanan | Hash kata sandi, CSRF, validasi server, pembatasan percobaan login, otorisasi role di server, role tidak dapat diisi dari formulir, validasi isi berkas unggahan |
| Pengujian | 22 dari 25 tes lolos (dilaporkan pemilik); 3 kegagalan sudah diperbaiki; tes tambahan (foto, akun admin, uji asap halaman) belum dijalankan |

5. **Kendala dan solusi** (semuanya tercatat pada percakapan pengembangan):

| Kendala | Solusi |
|---|---|
| PHP XAMPP 8.0.30 tidak memenuhi syarat Laravel; Composer menolak paket lama | Memakai PHP 8.5 dan Laravel terbaru |
| Tiga tes gagal: email ganda pada fixture dan tabel tidak ada pada tes bawaan | Fixture dibuat unik dan `RefreshDatabase` dipasang; constraint unik tidak dilonggarkan |
| Seluruh tes tidak berjalan karena nama fungsi bantu bentrok dengan `TestCase` | Fungsi diganti nama |
| `MissingAppKeyException` | `php artisan key:generate` |
| Gaya kartu produk tidak tampil di katalog | CSS dipindah ke stylesheet global |
| Peringatan OpenSSL dimuat dua kali | Penyebab di konfigurasi PHP komputer; langkah di `TROUBLESHOOTING.md` (belum dikonfirmasi teratasi) |
| Push ke GitHub bermasalah | Penyebab belum diketahui; alur dan tabel galat di `GIT_WORKFLOW.md` (menunggu pesan galat) |

6. **Status pencapaian target:** tercapai sebagian. Database, autentikasi, CRUD produk/unit, dan SEO dasar sudah diimplementasikan; fitur transaksi belum.
7. **Rencana berikutnya:** lihat Minggu ke-5 pada `LOGBOOK_NOTES.md`.

## Laporan minggu depan (Minggu ke-5)
Struktur sama dengan tujuh bagian di atas. Isi hanya setelah pekerjaan dilakukan dan diuji.

## Bahan untuk laporan akhir (tersedia)
- Arsitektur dan database: `PROJECT_DOCUMENTATION.md`; desain: `DESIGN_SYSTEM.md`; pemetaan penilaian: `PJBL_CHECKLIST.md`; rencana: `ROADMAP.md`.
- Pengujian: berkas di `tests/Feature/` dan keluaran `php artisan test`.
- Bukti tangkapan layar: `SCREENSHOT_GUIDE.md`.
