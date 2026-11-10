# Checklist Penilaian PjBL — PITLABS

Sumber: PDF "Pre-Project Based-Learning" (Web Design and Programming, 20 Oktober 2026).
Status diperbarui setiap fase. ✅ selesai · 🟡 sebagian/simulasi · ⬜ belum.

## A. Aplikasi web (50 poin)

| # | Kriteria | Poin | Status | Rencana / catatan |
|---|---|---|---|---|
| 1 | Halaman login dan register | 5 | 🟡 | Sungguhan sejak 5A: database, hash kata sandi, validasi klien + server, CSRF, throttle, tes. Menunggu dijalankan dan diuji pemilik |
| 1b | Login pihak ketiga (Google) | 10 (menggantikan 5) | 🟡 | Tombol "Masuk/Daftar dengan Google" + alur simulasi sudah ada. Fase 5: Laravel Socialite + OAuth 2.0 (lihat bagian C) |
| 2 | Halaman depan | 5 | ✅ | Homepage Fase 3 |
| 3 | Dashboard | 5 | 🟡 | Dashboard pelanggan dan admin sudah ada (data contoh, Fase 4); data nyata di Fase 5 |
| 4 | Fitur utama (CRUD, saling terintegrasi) | 20 | ⬜ | Lihat bagian B |
| 5 | Fitur keamanan (enkripsi, autentikasi, dll.) | 5 | 🟡 | Lihat bagian D |
| 6 | SEO friendly | 5 | 🟡 | Lihat bagian E |
| 7 | Logout | 5 | 🟡 | `Auth::logout()` + invalidasi session + regenerasi token (5A); menunggu diuji |

## B. Fitur utama (minimal 4–6 fitur inti yang terintegrasi, aturan PjBL no. 3)
1. Katalog dan detail produk (hasil: Fase 4A, UI) — CRUD produk oleh admin di Fase 5.
2. Cek ketersediaan dan booking tanpa bentrok — aturan bentrok sudah diperagakan; backend Fase 5.
3. Checkout, invoice, pembayaran (QRIS/VA/COD) — UI selesai (Fase 4); payment gateway Fase 5. Gateway belum dipilih.
4. Pengambilan lewat QR invoice (admin) — UI selesai (Fase 4); logika Fase 5.
5. Pengembalian, inspeksi, denda — UI selesai (Fase 4); logika Fase 5.
6. Laporan keuangan — UI selesai (Fase 4); logika Fase 5.

Konsep CRUD yang harus terlihat saat demo: produk, unit rental, order/booking, inspeksi, denda, pengguna.

## C. Login Google (pihak ketiga) — apa artinya
"Pihak ketiga" = pengguna masuk memakai akun layanan lain (di sini Google) tanpa membuat kata sandi baru. PITLABS tidak menyimpan kata sandi Google; Google mengirim identitas (nama, email) setelah pengguna setuju. Standarnya OAuth 2.0.
Yang disiapkan di Fase 5:
- Paket `laravel/socialite`.
- Project di Google Cloud Console → OAuth consent screen → OAuth client ID (Web).
- Authorized redirect URI, contoh lokal `http://127.0.0.1:8000/auth/google/callback` dan produksi `https://DOMAIN/auth/google/callback`.
- `.env`: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` (jangan di-commit).
- Kolom `google_id` pada tabel users; kata sandi boleh kosong untuk akun Google.

## D. Keamanan (dasar yang diminta aturan PjBL no. 7)
| Item | Status |
|---|---|
| Validasi input sisi klien dan server | ✅ form login, daftar, cek ketersediaan |
| Proteksi CSRF pada form POST | ✅ `@csrf` |
| Escape output (XSS) | ✅ Blade `{{ }}` |
| Batas percobaan login (anti brute force) | ✅ `throttle:6,1` |
| Cegah open redirect pada tautan "kembali" | ✅ `Pit::safePath` + tes |
| Regenerate session saat login/logout | ✅ |
| Mass-assignment `role`/`is_active` dicegah | ✅ + tes |
| Hash kata sandi | ✅ cast `hashed` (5A) |
| Otorisasi admin di server (middleware) | ✅ `EnsureRole` + tes (5A) |
| Query aman (Eloquent/prepared statement) | 🟡 dipakai pada auth/pelanggan; sisanya mengikuti batch berikutnya |
| HTTPS, header keamanan, `.env` tidak ter-commit | ⬜ Fase 6–7 |
| Verifikasi signature webhook pembayaran | ⬜ Fase 5 |

## E. SEO (URL, gambar, sitemap, meta description, internal/external link, konten, keamanan, kecepatan)
| Item | Status | Catatan |
|---|---|---|
| URL bersih | ✅ `/katalog`, `/produk/{slug}`, `/cek-ketersediaan` |
| `<title>` dan meta description unik per halaman | ✅ |
| Canonical, Open Graph | ✅ |
| `sitemap.xml` | ✅ dinamis (`/sitemap.xml`); sumber data diganti database di Fase 5 |
| `robots.txt` | ✅ ganti `DOMAIN-ANDA` saat deploy |
| Data terstruktur | ✅ Organization (homepage), BreadcrumbList (katalog, detail, ketersediaan) |
| Hierarki heading (satu H1 per halaman) | ✅ |
| Teks alternatif gambar | 🟡 logo ✅; foto produk menunggu fotonya |
| Tautan internal | ✅ breadcrumb, produk terkait, footer |
| Tautan eksternal | 🟡 tombol Shopee memakai `rel="noopener noreferrer sponsored"`, menunggu URL. Tambahkan rujukan resmi saat membuat Artikel |
| Konten | 🟡 deskripsi/spesifikasi produk menunggu data (lihat `DATA_PRODUK_YANG_DIBUTUHKAN.md`); Artikel belum ada |
| Kecepatan | 🟡 JS `defer`, font `display=swap`, ukuran logo ditetapkan. Menyusul: gambar WebP + `loading="lazy"`, kompresi/cache server, tes Lighthouse (Fase 6) |
| Keamanan (HTTPS) | ⬜ Fase 7 |

## F. Penilaian lain
- **Logbook (35):** isi setiap pekan pada hari mata kuliah (pukul 19.00 WITA) **dengan dokumentasi** (nilai 5 vs 2). Tangkapan layar halaman, daftar commit, dan `DEVELOPMENT_LOG.md` bisa jadi bukti.
- **Kerja sama tim (15):** tiap anggota harus punya kontribusi yang bisa dibuktikan di GitHub. Bagi modul per anggota dan commit dengan akun masing-masing (lihat `TEAM_WORKFLOW.md`).

## G. Dokumen yang dikumpulkan
| Dokumen | Status |
|---|---|
| PRD (dengan tanda tangan mitra, pekan 10) | 🟡 PRD ada; tanda tangan mitra perlu dipastikan |
| UI/UX: **Figma**, user flow, design system | 🟡 `DESIGN_SYSTEM.md` ada. **Figma dan user flow belum**: PjBL menyebut Figma secara khusus |
| Source code (tautan GitHub) | 🟡 berjalan |
| Dokumentasi teknis (arsitektur, rancangan database, modul) | 🟡 ada di Documentation Pack, disesuaikan setelah backend |
| Testing report | ⬜ `TEST_PLAN.md` ada; hasil uji Fase 6 |
| Presentasi + video demo 10 menit | ⬜ Fase 7 |
| Hosting | ⬜ PjBL menyebut VPS Sumopod / hosting Rumahweb |

## H. Jadwal PjBL
Pekan 10 TTD kerja sama dan wawancara · 11–12 UI/UX · 13–14 fitur utama, CRUD, database, SEO · 15 integrasi pihak ketiga (autentikasi, payment gateway), pengujian, perbaikan bug · 16 presentasi dan demo.
