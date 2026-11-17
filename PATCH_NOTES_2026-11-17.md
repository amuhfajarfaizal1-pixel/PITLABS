# PITLABS — Patch operasional 17-11-2026

Patch ini berisi perubahan source saja, bukan proyek Laravel utuh. Terapkan dengan menyalin isi ZIP ke root proyek PITLABS penuh dan timpa file yang sama.

## Fitur dalam source
- Rekonsiliasi non-destruktif tabel inti 5C-1 bila migration lama sudah tercatat tetapi tabel terhapus. Periksa status dengan `php artisan migrate:status` dan isi tabel melalui phpMyAdmin.
- Backend admin berbasis database untuk pembayaran COD, pemindaian/verifikasi invoice, pickup, return, inspeksi, pencatatan denda, antrean, dashboard dan laporan. Pembayaran COD yang belum memiliki tarif harus diisi admin dengan jumlah yang benar-benar diterima sesuai kesepakatan mitra.
- Hasil verifikasi scanner berlaku lima menit dan dihapus setelah pickup/scan gagal. QR kamera memakai Browser BarcodeDetector; input manual menerima nomor invoice atau payload QR. QR payload diperiksa HMAC dan token hash pada database. Scan tidak mengubah status; pickup tetap aksi terpisah.
- Denda tidak memiliki rumus/tarif otomatis. Admin memasukkan nominal hanya setelah keputusan sesuai kebijakan mitra; pencatatan lunas memerlukan nominal diterima yang sama persis dengan nilai denda yang disetujui.
- Unit yang baru dikembalikan langsung ditahan dalam status perawatan sampai inspeksi; unit hanya kembali tersedia setelah kondisi dinilai baik.
- KPI unit tersedia menghitung unit sibuk secara unik dan mengecualikan inventaris dari produk nonaktif/non-rental.
- Light/dark mode tersimpan di localStorage pada layout publik dan dashboard; aksen admin dan pelanggan dipertahankan.
- Detail produk memerlukan login dan login diarahkan kembali ke detail yang diminta.
- DatabaseSeeder hanya menjalankan kategori dasar. ProductSeeder, InventoryUnitSeeder, dan UserSeeder lama dibuat no-op dengan peringatan agar pemanggilan manual lama pun tidak membuat data contoh.

## Batasan yang tetap ada
- QRIS/Virtual Account dan payment gateway/webhook tidak diimplementasikan; hanya COD dapat diverifikasi manual.
- Penjualan Shopee eksternal tidak diimpor ke laporan.
- Notifikasi WhatsApp/email tidak dikirim otomatis.
- Nominal denda/tarif tidak diisi sistem karena belum ada kebijakan mitra yang terverifikasi.
- Produk/unit/pesanan tidak dihapus permanen sesuai AGENTS.md; produk dinonaktifkan, unit dipensiunkan.

## Pemasangan dan pengujian
1. Cadangkan proyek dan database.
2. Salin file patch ke root proyek Laravel penuh.
3. Pastikan MySQL aktif dan `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` pada `.env` sesuai database yang dibuka di phpMyAdmin.
4. Jalankan `php artisan optimize:clear`, lalu `php artisan migrate:status`.
5. Jalankan `php artisan migrate` (jangan `migrate:fresh`).
6. Jalankan `php artisan test`.
7. Uji dengan akun admin dan produk/unit yang benar-benar dimasukkan dari data mitra.

Arsip sumber yang ditinjau tidak menyertakan `artisan`, `composer.json`, `bootstrap/`, `.env`, atau `vendor/`; karena itu pengujian integrasi tidak dapat dijalankan di lingkungan penyusunan patch ini. Jangan menganggap deployment siap sebelum tes pada instalasi lengkap lolos.
