# Roadmap PITLABS

Status: frontend lengkap sampai Fase 4; backend dikerjakan bertahap. Setiap batch menunggu persetujuan pemilik (lihat [AGENTS.md](../AGENTS.md)). Jadwal PjBL: pekan 13–14 fitur utama, CRUD, database, SEO; pekan 15 integrasi pihak ketiga dan pengujian; pekan 16 presentasi ([PJBL_CHECKLIST.md](PJBL_CHECKLIST.md)).

## Selesai
| Fase | Hasil |
|---|---|
| 1–3 | Analisis, design system, homepage |
| 4 | Seluruh halaman publik, pelanggan, admin (tampilan) |
| 5A | Database, autentikasi, role, profil |
| 5B | Katalog dari database, CRUD produk dan unit |
| 5 (perbaikan) | Hero, tema admin Navy × Yellow, perbaikan tes |

## Berikutnya
> **5C ditunda sampai data dari mitra tersedia** (durasi sewa min/maks, waktu tahan unit, tarif, denda). Sambil menunggu, pekerjaan yang tidak bergantung pada mitra: isi produk dan unit dari menu admin, Figma dan user flow, PRD bertanda tangan, pengujian dan perbaikan bug 5A–5B, serta persiapan hosting dan kredensial Google.

| Batch | Isi | Hasil yang dapat didemokan | Bergantung pada |
|---|---|---|---|
| **5C. Booking dan checkout** | Tabel pesanan dan reservasi; ketersediaan dihitung dari database; pencegahan bentrok di server (transaksi + kunci); checkout membuat pesanan dan invoice bernomor unik; token QR invoice; pembayaran COD manual | Pelanggan memesan, invoice terbit, dua pelanggan tidak bisa mengambil unit yang sama | Aturan durasi min/maks dan waktu tahan unit dari mitra |
| **5D. Operasional admin** | Verifikasi pembayaran, verifikasi pengambilan (validasi token QR), pengembalian, pemeriksaan kondisi, denda, transisi status tervalidasi, log audit | Alur sewa penuh dari pesan sampai selesai | Nominal denda dan tarif keterlambatan dari mitra |
| **5E. Data nyata di dashboard** | Dashboard pelanggan dan admin, antrean rental, laporan keuangan dari database; penjadwal status Mendekati batas dan Terlambat; pengingat email | Semua angka dan tabel berasal dari database | 5C dan 5D |
| **5F. Integrasi pihak ketiga** | Login Google (Socialite, +10 poin); payment gateway QRIS/VA dengan webhook bertanda tangan dan idempotensi | Masuk dengan Google; bayar online lunas otomatis | **Keputusan** gateway, akun dan kredensial Google Cloud, akun gateway |
| **6. Kualitas dan keamanan** | Tes fitur lengkap, uji lintas peramban dan perangkat, aksesibilitas, header keamanan (CSP, HSTS), kebijakan otorisasi, Lighthouse dan optimasi gambar (WebP, lazy-load), perbaikan bug, laporan pengujian | Testing report | Foto produk final |
| **7. Rilis dan presentasi** | Deploy ke VPS Sumopod atau hosting Rumahweb (HTTPS, `.env` produksi, backup), domain di `robots.txt` dan sitemap, dokumentasi akhir, video demo 10 menit, presentasi | Aplikasi online dan bahan presentasi | Domain dan hosting |

## Jalur paralel (tidak menunggu kode)
- **Data dari mitra:** tarif, denda, deposit, durasi, alamat, kontak, URL Shopee, foto, spesifikasi ([DATA_PRODUK_YANG_DIBUTUHKAN.md](DATA_PRODUK_YANG_DIBUTUHKAN.md)). Admin dapat mengisinya langsung dari menu Produk dan Inventaris.
- **Dokumen PjBL:** PRD bertanda tangan mitra, **Figma dan user flow** (belum ada), dokumentasi teknis, testing report.
- **Tim:** logbook mingguan dengan dokumentasi, commit per anggota ([GIT_WORKFLOW.md](GIT_WORKFLOW.md), [PEMBAGIAN_MODUL.md](PEMBAGIAN_MODUL.md)).

## Keputusan yang masih terbuka
1. Payment gateway (pilihan tim; dibutuhkan sebelum 5F).
2. Kredensial Google OAuth (pemilik membuat project di Google Cloud Console).
3. Hosting akhir: VPS Sumopod atau Rumahweb.
4. Kebijakan mitra: denda, deposit, durasi, pembatalan dan refund.
5. Cara layanan Google Colab dioperasikan (aktivasi dan batas slot).

## Prioritas bila waktu mepet
Utamakan alur yang dinilai langsung: **5C (booking) → 5D (operasional) → Google login → pembayaran COD** sudah memenuhi CRUD, fitur terintegrasi, dan keamanan. Gateway pembayaran online dan pengingat WhatsApp dapat menjadi tahap lanjutan.
