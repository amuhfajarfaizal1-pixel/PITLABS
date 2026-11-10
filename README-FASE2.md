# PITLABS — cara memasang (kumulatif Fase 2–4B)

1. Project Laravel (PHP 8.2+): `composer create-project laravel/laravel pitlabs`
2. Ekstrak paket ini ke root project, pilih **timpa**.
3. `.env`: `SESSION_DRIVER=file`, `APP_ENV=local`
4. `php artisan view:clear` dan `php artisan route:clear`, lalu `php artisan serve`
5. Buka `http://127.0.0.1:8000/masuk` dan pakai **akses cepat demo**:
   - **Admin demo** → `/admin` (tema kuning): New Orders, Ongoing Rentals, verifikasi pembayaran, QR Scanner (coba INV-CONTOH-004), pengambilan, pengembalian, pemeriksaan, denda, produk, inventaris, laporan.
   - **Pelanggan demo** → `/dashboard`: ringkasan, penyewaan aktif, riwayat, pengembalian, profil.
   - Alur sewa: `/produk/jetson-orin-nano` → Sewa sekarang → cek ketersediaan → Lanjutkan → `/checkout` → invoice.
6. Publik: `/`, `/katalog`, `/faq`, `/panduan`, `/tentang-kami`, `/artikel`, `/sitemap.xml`, `/design-system`.

Butuh internet (CDN Bootstrap, Bootstrap Icons, Google Fonts). Semua data dashboard adalah data contoh.
