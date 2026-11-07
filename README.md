# PITLABS — Static HTML/CSS Prototype

Versi ini dibuat khusus untuk preview di GitHub Pages.

## Deploy ke GitHub Pages
1. Extract isi ZIP.
2. Upload seluruh isi folder ini ke root repository GitHub (pastikan `index.html` berada di root).
3. Buka **Settings → Pages**.
4. Pada Build and deployment pilih **Deploy from a branch**.
5. Pilih branch `main` dan folder `/ (root)`, lalu Save.
6. Tunggu deployment selesai; buka URL GitHub Pages yang diberikan GitHub.

## Halaman prototype
- `index.html` — Beranda
- `catalog.html` — Katalog
- `product.html` — Detail produk
- `checkout.html` — Checkout rental
- `invoice.html` — Invoice
- `admin.html` — Dashboard admin
- `account.html` — Dashboard pelanggan
- `login.html`, `register.html` — Autentikasi visual
- `ai.html`, `articles.html`, `faq.html` — Fitur pendukung

## Batasan versi statis
Versi ini hanya HTML dan CSS (tanpa JavaScript/PHP/backend). Link navigasi antarhalaman berfungsi, tetapi pencarian/filter, login, pembayaran, QR scan, penyimpanan transaksi, stok real-time, countdown, notifikasi WhatsApp, dan CRUD belum aktif. Data yang ditampilkan merupakan data contoh untuk presentasi UI/UX.

Project PHP + MySQL asli tetap disimpan terpisah dan dijalankan melalui XAMPP/hosting yang mendukung PHP dan database. GitHub Pages tidak menjalankan PHP atau MySQL.
