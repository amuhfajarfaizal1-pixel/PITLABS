# Design System — PITLABS (Fase 2)

Dokumen ini melengkapi `UI_UX_GUIDELINES.md`. Implementasi: `public/assets/css/pitlabs.css`, komponen di `resources/views/components/`, referensi hidup di route `/design-system` (hanya environment `local`).

**Stack:** Laravel (Blade) + Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 + CSS token PITLABS. Tidak ada build step; Bootstrap dimuat dari CDN (versi dipinning).

## 1. Prinsip
1. Bersih dan teknis, bukan ramai. Dekorasi hanya di hero (gradien dibatasi di sana).
2. Satu radius per peran, satu bayangan per tingkat.
3. Status selalu teks + ikon, tidak hanya warna.
4. Data yang belum diputuskan mitra tidak dikarang: pakai `<x-pending>`.
5. Oranye hanya untuk tombol Beli via Shopee.

## 2. Warna
| Token | Hex | Peran | Catatan kontras |
|---|---|---|---|
| `--pit-navy` | #0A2540 | Judul, hero, footer, sidebar admin | Teks putih di atasnya 15:1 |
| `--pit-sapphire` | #1463FF | CTA sewa, tautan, fokus | Putih di atasnya 4,9:1 |
| `--pit-cyan` | #00A3FF | Aksen, ikon, eyebrow **di atas navy** | Di latar putih hanya 2,7:1: jangan dipakai untuk teks |
| `--pit-turquoise` | #00F5D4 | Sorotan kecil di atas navy | Jangan untuk teks di latar putih |
| `--pit-gray` | #E9ECEF | Border, latar sekunder | — |
| `--pit-orange` | #F97316 | Hanya `.btn-shopee` | Teks **navy** (5,5:1). Putih hanya 2,8:1. Provisional sampai mitra setuju |
| `--pit-text` | #2F4358 | Teks isi | 10:1 |
| `--pit-muted` | #566879 | Teks sekunder | 5,7:1 |

Warna status (badge dan alert): success (hijau), warning (**kuning**, bukan oranye), danger (merah), info (sapphire), progress (biru laut), neutral (abu). Seluruh pasangan teks/latar ≥ 5,8:1. Batas kolom form #7D90A3 (3,3:1 terhadap putih).

## 3. Tipografi
| Peran | Font | Catatan |
|---|---|---|
| Judul, display | Sora 600–800 | `.pit-display` untuk headline hero, h1–h4 fluid dengan `clamp()` |
| Teks isi | Plus Jakarta Sans 400–700 | 16 px, line-height 1,65, lebar baca maks. 68 karakter (`.pit-prose`) |
| Kode unit, nomor invoice | JetBrains Mono | `.pit-mono` |

Eyebrow memakai sentence case: cyan di atas navy, sapphire di latar terang.

## 4. Spasi, radius, bayangan
- Spasi: kelipatan 4 px. Bootstrap `1–5` = 4/8/16/24/48 px. Jarak antar section: `--pit-section-y` (48–88 px, fluid).
- Radius: sm 8 (badge) · md 12 (tombol, input) · lg 16 (kartu) · xl 24 (modal, panel hero).
- Bayangan: `--pit-shadow-sm` (kartu diam), `--pit-shadow-md` (disorot, dropdown, modal).
- Touch target minimal 44 × 44 px (tombol, tautan nav, input 48 px).

## 5. Komponen
| Komponen | Pemakaian | Catatan |
|---|---|---|
| Tombol | `.btn-primary` · `.btn-outline-primary` · `.btn-soft` · `.btn-navy` · `.btn-ghost` · `.btn-on-dark` · `.btn-shopee` | Satu tombol utama per area. Loading: `data-pit-loading` |
| Field | `<x-field name label type required hint error />` | Label eksplisit, `aria-describedby`, pesan error di bawah kolom |
| Form | `<form data-pit-validate data-pit-simulate>` | Validasi sisi klien. `data-pit-simulate` = tidak dikirim (Fase 2) |
| Badge status | `<x-status-badge group="payment" status="UNPAID" />` | Label dari `config/pitlabs.php`; prefix tersembunyi untuk pembaca layar |
| Alert | `<x-alert type="info\|success\|warning\|danger" title>` | `role="alert"` hanya untuk danger |
| State kosong | `<x-empty-state icon title>` + slot `action` | Selalu tawarkan langkah berikutnya |
| Modal | `<x-modal id title>` + slot `footer` | Fokus terkunci, Escape menutup, fokus kembali ke pemicu (Bootstrap) |
| Data pending | `<x-pending field="…" />` | Cari semua: `grep -r "data-needs-data" resources/views` |
| Skeleton | `.pit-skeleton--line` / `--block` | State loading |
| Checkbox ketentuan | `<x-terms-checkbox id="terms" />` | Teks ketentuan berupa tautan biru (`.pit-link`) ke `#ketentuan-sewa`, dibuka di tab baru agar isian form tidak hilang |
| Kartu produk | `<x-product-card :product="$product" />` | Hanya gambar, nama, kategori, harga. Tanpa tombol Beli/Sewa. Tanpa foto → ubin ikon; harga → `<x-pending>` |
| Filter grid | `[data-pit-filter]` + `button[data-filter]` + `data-type` pada item | Pengumuman jumlah hasil lewat `aria-live` |
| Accordion FAQ | `.accordion.pit-accordion` | Komponen Bootstrap bergaya PITLABS |
| Stepper cara sewa | `ol.pit-steps` | Bernomor karena urutannya memang berurutan; horizontal di ≥ 992 px |
| Breadcrumb | `<x-breadcrumb :items="[[label, url\|null], …]" />` | Sekaligus menulis JSON-LD BreadcrumbList |
| Field kata sandi | `<x-field type="password" …>` | Otomatis memuat tombol tampil/sembunyikan; konfirmasi: `data-match` |
| Tombol Google | `.btn-google` | Putih, border jelas; dipakai di login dan daftar |
| Navbar / Footer | `partials/navbar`, `partials/footer` | Navigasi dari `config/pitlabs.php` |
| AI Technical Assistant | `partials/ai-assistant` | Tombol melayang + modal "Segera Hadir"; bukan chatbot |

## 6. Aturan logo
Logo resmi tidak digambar ulang. Huruf "IT" berwarna navy, jadi di latar navy logo selalu diletakkan di atas panel putih (lihat `.pit-footer__logo`).

## 7. Aksesibilitas yang sudah diterapkan
Skip link, fokus terlihat (cincin sapphire; turquoise di latar navy), `aria-current` pada menu aktif, `aria-describedby`/`aria-invalid` pada field, `prefers-reduced-motion` dihormati, badge tidak mengandalkan warna.

## 8. Homepage (Fase 3)
Urutan: hero → produk (filter jenis) → cara sewa → ketentuan sewa dan denda → FAQ ringkas → CTA penutup. Gradien dan pola grid hanya dipakai di hero. Bagian `#ketentuan-sewa` adalah tujuan tautan checkbox persetujuan; saat halaman Panduan dibuat, bagian itu dipindah ke sana dan `App\Support\Pit::termsUrl()` otomatis mengarah ke Panduan.

## 9. Belum dikerjakan
Halaman fungsional apa pun (Fase 3+), tabel data admin, sidebar admin, kartu produk final, kalender ketersediaan.


## 10. Dashboard (Fase 4)
Satu kerangka (`layouts/dashboard`, `public/assets/css/dashboard.css`), dua tema lewat variabel `--dash-*`.

| Peran warna | Pelanggan (`.pit-theme-user`) | Admin (`.pit-theme-admin`) |
|---|---|---|
| Aksen / tombol utama | Sapphire #1463FF, teks putih | Bright Yellow #FFCE1F, teks navy #0A2540 (10,4:1) |
| Sidebar | Putih, aktif Sapphire-soft | Charcoal #0F1826, aktif kuning + teks navy |
| Teks tautan / aksen di latar terang | Sapphire | Kuning tua #5E4400 (9:1 di putih) |
| Fokus | Cincin Sapphire | Outline navy 3 px (kuning terlalu rendah kontras) |
| Latar halaman | #F3F5F8 | #F3F5F8 |

Aturan: kuning dipakai untuk identitas, navigasi aktif, tombol utama, dan sorotan, bukan untuk seluruh halaman. Warna semantik (hijau/merah/biru laut/kuning-peringatan) tetap sama di kedua tema; status selalu ikon + teks sehingga tidak tertukar dengan kuning identitas admin. Tanpa gradien, glow, atau glassmorphism di dashboard.
Komponen dashboard: `x-dash.page-header`, `stat`, `panel`, `table` (cari, filter, aksi simulasi), `bars` (grafik SVG tanpa pustaka). Tabel di layar kecil menggulir horizontal.
