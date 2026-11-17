# Roadmap PITLABS

Status source per 17 November 2026. “Sudah diimplementasikan” berarti perubahan source tersedia; status “sudah diuji” hanya diberikan setelah pengujian pada instalasi Laravel lengkap dan alur MySQL berhasil.

## Fase yang sudah tersedia pada source

| Fase | Cakupan | Status |
|---|---|---|
| 1–4 | Analisis, design system, homepage, halaman publik, pelanggan dan admin | Sudah diimplementasikan |
| 5A | Database dasar, autentikasi, role, profil | Sudah diimplementasikan; tes terdahulu perlu regresi |
| 5B | Katalog database, CRUD produk/unit, upload foto, SEO dasar | Sudah diimplementasikan; produk/unit berasal dari data mitra |
| 5C-1 | Booking, reservasi, anti-bentrok, checkout COD, invoice, token QR | Sudah diimplementasikan; perlu uji integrasi MySQL |
| 5C-2 | Verifikasi COD, QR scanner kamera/manual, pengambilan, pengembalian, inspeksi, denda, antrean | Sudah diimplementasikan pada source 17-11; belum diuji end-to-end |
| 5E | Dashboard admin dan laporan keuangan berdasarkan data DB | Sudah diimplementasikan pada source 17-11; belum direkonsiliasi dengan data produksi |
| Tema | Light/dark mode untuk situs publik dan dashboard | Sudah diimplementasikan pada source 17-11; perlu uji visual responsif |

## Pekerjaan berikutnya

| Batch | Isi | Prasyarat |
|---|---|---|
| 5C/5D/5E verifikasi | Jalankan migration, tes otomatis, simulasi alur dengan akun dan data inventaris resmi, perbaiki bug yang ditemukan | Proyek Laravel lengkap, MySQL aktif, APP_KEY dan Composer dependencies |
| 5F integrasi pihak ketiga | Google OAuth; QRIS/Virtual Account + webhook bertanda tangan/idempotensi | Keputusan provider, akun dan kredensial resmi |
| Operasional kebijakan | Tarif sewa, denda, deposit, pembatalan/refund, durasi min/maks | Dokumen/kesepakatan mitra yang terverifikasi |
| 6 kualitas dan keamanan | Uji lintas-browser/perangkat, aksesibilitas, otorisasi, audit keamanan, Lighthouse, backup | Fitur dan data mitra stabil |
| 7 rilis | Deploy HTTPS, `.env` produksi, backup, domain/sitemap, dokumentasi dan demo | Domain/hosting diputuskan |

## Prinsip data dan transaksi

- Jangan menambahkan produk, unit, akun pelanggan, pesanan, antrean, atau nilai transaksi contoh.
- Tarif dan denda yang belum disahkan tetap ditampilkan sebagai informasi belum tersedia atau menunggu penilaian.
- QR scan hanya memverifikasi identitas invoice; status pengambilan berubah hanya setelah admin menekan konfirmasi.
- Status pesanan, pembayaran, pengambilan, dan penyewaan tetap terpisah.
- Produk, unit, dan pesanan tidak dihapus permanen; gunakan nonaktif/arsip/pensiun agar histori terjaga.
- Pembelian Shopee eksternal tidak dihitung sebagai transaksi rental internal.

## Keputusan yang masih diperlukan dari mitra/pemilik

1. Provider dan kredensial payment gateway QRIS/VA.
2. Kredensial Google OAuth bila login Google masih menjadi target.
3. Tarif sewa, deposit, durasi min/maks, denda, pembatalan dan refund.
4. Keputusan hosting/domain akhir.
5. Aturan pengingat dan kanal komunikasi pelanggan (email/WhatsApp).
