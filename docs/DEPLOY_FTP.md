# Panduan Deploy PITLABS lewat FTP (tanpa SSH, tanpa Composer di server)

Situasi: dosen memberi akses **FTP saja** dan domain sudah tersedia. Server tidak bisa menjalankan `composer` maupun `php artisan`. Karena itu:
- **Composer dijalankan di komputer Anda**, lalu folder `vendor/` ikut diunggah.
- Perintah `artisan` (migrate, seed) **tidak dapat dijalankan di server**; database disiapkan lewat impor SQL (lihat bagian 6).
- Domain harus mengarah ke folder `public` milik Laravel, bukan ke akar proyek.

## 1. Informasi yang harus Anda minta dari dosen/penyedia hosting
Tanpa ini deploy bisa gagal di tengah jalan. Mohon dijawab:
| # | Pertanyaan | Mengapa penting |
|---|---|---|
| 1 | Versi PHP server dan apakah bisa dipilih? Versi minimal ada pada `composer.json` baris `"php"` | Jika PHP server lebih rendah, aplikasi menolak berjalan |
| 2 | Ada MySQL/MariaDB? Host, nama database, user, kata sandi. Ada phpMyAdmin? | Database tempat data disimpan |
| 3 | Domain mengarah ke folder apa (`public_html`, `htdocs`, `www`)? Bisakah mengunggah file di **atas** folder itu? | Menentukan letak folder Laravel (bagian 2) |
| 4 | Domain sudah HTTPS? | Kamera scan invoice hanya berfungsi lewat HTTPS |
| 5 | Ekstensi PHP aktif: mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, fileinfo | Dibutuhkan Laravel |
| 6 | Apakah `.htaccess` dan mod_rewrite aktif (Apache)? Atau server Nginx? | Rute Laravel (`/katalog`, `/masuk`) bergantung padanya |

Cek cepat PHP server: unggah `info.php` berisi `<?php phpinfo();` ke folder domain, buka di browser, catat versi dan ekstensi, lalu **hapus file itu segera**.

## 2. Pilih tata letak folder
**Tata letak A (disarankan): folder Laravel di luar folder web.** Aman karena `.env` dan kode tidak dapat diakses lewat browser.
```
/ (akar FTP)
├── laravel/        ← seluruh proyek (app, vendor, config, .env, storage, ...), TANPA folder public
└── public_html/    ← isi folder public (index.php, .htaccess, assets/, uploads/, robots.txt)
```
**Tata letak B: hanya bisa mengunggah di dalam folder web.**
```
public_html/
├── laravel/        ← seluruh proyek; WAJIB berisi .htaccess penolak akses (lihat bawah)
├── index.php       ← dari public/
├── .htaccess, assets/, uploads/, robots.txt
```
Isi `laravel/.htaccess` pada tata letak B:
```
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Deny from all
</IfModule>
```
Setelah mengunggah, uji `https://DOMAIN/laravel/.env` dan `https://DOMAIN/laravel/composer.json`: keduanya **harus ditolak (403/404)**.

## 3. Menyiapkan paket di komputer (jangan di folder kerja)
`composer install --no-dev` menghapus paket pengujian, jadi jalankan di **salinan**. Contoh PowerShell (sesuaikan jalur):
```powershell
$src = "C:\Users\Lenovo\Documents\pitlabs"
$out = "C:\Users\Lenovo\Documents\pitlabs-deploy"

# 1) salin proyek tanpa berkas yang tidak perlu dan TANPA .env
robocopy $src "$out\laravel" /E /XD .git vendor node_modules tests docs scripts /XF .env *.zip

# 2) pasang dependensi produksi di salinan
cd "$out\laravel"
composer install --no-dev --optimize-autoloader

# 3) buat kunci aplikasi (catat hasilnya: base64:...)
php artisan key:generate --show

# 4) pindahkan folder public keluar (tata letak A)
Move-Item "$out\laravel\public" "$out\public_html"
```
**Edit `public_html\index.php`** (tata letak A; ada tiga tempat `'/../`):
```php
// ganti:  __DIR__.'/../storage/framework/maintenance.php'
// jadi:   __DIR__.'/../laravel/storage/framework/maintenance.php'
// ganti:  __DIR__.'/../vendor/autoload.php'       → __DIR__.'/../laravel/vendor/autoload.php'
// ganti:  __DIR__.'/../bootstrap/app.php'          → __DIR__.'/../laravel/bootstrap/app.php'

// dan tambahkan SETELAH baris "$app = require_once ...":
$app->usePublicPath(__DIR__);
```
Baris `usePublicPath` **wajib**: tanpa itu Laravel mencari folder `public` di dalam `laravel/`, sehingga foto produk tersimpan di tempat yang salah dan tidak tampil. Untuk tata letak B ganti `'/../` menjadi `'/laravel/`.

Hapus juga `laravel\bootstrap\cache\*.php` bila ada, dan pastikan folder berikut ada (kosong): `laravel\storage\framework\cache\data`, `\sessions`, `\views`, `\logs`, `laravel\bootstrap\cache`, `public_html\uploads\products`.

**Jangan** menjalankan `php artisan config:cache` di komputer sebelum mengunggah: cache itu menyimpan jalur absolut Windows dan akan merusak aplikasi di server.

## 4. Mengunggah lewat FTP
- Gunakan FileZilla. Pengaturan: mode **pasif**, jenis transfer **biner**/otomatis, 4–8 koneksi simultan, aktifkan percobaan ulang.
- Urutan: `laravel/` dulu (folder `vendor/` berisi ribuan berkas dan dapat memakan puluhan menit), baru `public_html/`.
- Bila penyedia hosting punya File Manager dengan fitur "Extract", jauh lebih cepat: `tar -a -c -f laravel.zip laravel` (di Windows 10+), unggah satu berkas zip, lalu ekstrak di server.
- Jika unggahan terputus, lanjutkan dengan opsi "timpa jika ukuran/waktu berbeda".

## 5. Berkas `.env` di server
Buat berkas bernama `.env` di dalam `laravel/` (tulis sebagai `env.txt` lalu ganti nama di server bila klien FTP menyembunyikan titik di depan nama):
```
APP_NAME=PITLABS
APP_ENV=production
APP_KEY=base64:HASIL_key:generate_--show
APP_DEBUG=false
APP_URL=https://DOMAIN-ANDA
APP_LOCALE=id

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=ISI_DARI_HOSTING
DB_PORT=3306
DB_DATABASE=ISI_DARI_HOSTING
DB_USERNAME=ISI_DARI_HOSTING
DB_PASSWORD=ISI_DARI_HOSTING

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
CACHE_STORE=file
QUEUE_CONNECTION=sync
```
`APP_DEBUG` **harus false** di produksi. Jangan pernah mengunggah `.env` komputer Anda dan jangan membagikan isinya.

## 6. Database tanpa `artisan`
**Opsi 1 (disarankan bila ada phpMyAdmin):**
1. Di komputer, pada database lokal yang sudah `php artisan migrate --seed`, buat akun admin dengan kata sandi kuat: `php artisan pitlabs:create-admin`.
2. phpMyAdmin lokal → pilih database `pitlabs` → **Export** → format SQL → unduh.
3. phpMyAdmin server → pilih database hosting → **Import** → pilih berkas SQL.
4. Masuk sebagai admin di situs, lalu ganti kata sandi lewat `/dashboard/profil`.
Hapus akun pelanggan uji dari berkas SQL atau dari tabel `users` bila tidak diperlukan.

**Opsi 2: tanpa phpMyAdmin.** Perlu fitur halaman setup terjaga token untuk menjalankan migrasi dari browser. Fitur ini belum dibuat dan memerlukan persetujuan Anda (lihat pertanyaan pada pesan pengembangan).

**Opsi 3: jika server tidak punya MySQL.** SQLite dapat dipakai (`DB_CONNECTION=sqlite`) bila `pdo_sqlite` aktif dan folder `laravel/database` dapat ditulis. Perlu persetujuan karena memengaruhi struktur data produksi.

## 7. Izin tulis (chmod)
Di FileZilla: klik kanan folder → **Izin berkas** → `775`, centang "ubah subfolder". Terapkan pada: `laravel/storage`, `laravel/bootstrap/cache`, `public_html/uploads/products`. Bila masih galat izin, tanyakan ke penyedia hosting; hindari `777` pada folder yang dapat diakses web.

## 8. Uji setelah deploy
1. `https://DOMAIN/` tampil lengkap dengan CSS.
2. `https://DOMAIN/katalog`, `/produk/...`, `/sitemap.xml` terbuka (rute bersih berarti rewrite aktif).
3. Daftar akun, masuk, lalu keluar.
4. Masuk sebagai admin: `/admin`, tambah produk, unggah foto, foto tampil di katalog.
5. `https://DOMAIN/.env` dan `https://DOMAIN/laravel/.env` ditolak.
6. Ganti `DOMAIN-ANDA` pada `public_html/robots.txt`.

## 9. Pembaruan berikutnya (hanya file yang berubah)
Unggah file yang berubah ke jalur padanannya:
| Di proyek | Di server (tata letak A) |
|---|---|
| `app/`, `config/`, `resources/`, `routes/`, `database/` | `laravel/app/`, `laravel/config/`, … |
| `public/assets/...`, `public/robots.txt` | `public_html/assets/...`, `public_html/robots.txt` |
| `public/index.php` | **jangan ditimpa** (sudah dimodifikasi) |
| Paket baru di `vendor/` (jika `composer.json` berubah) | jalankan `composer install --no-dev` di salinan deploy, unggah `vendor/` |
Setelah mengunggah, hapus isi `laravel/storage/framework/views/` agar tampilan dikompilasi ulang. Jika pembaruan memuat **migration baru**, tabelnya harus dibuat lewat SQL: ekspor struktur dari database lokal dan impor ke server (atau gunakan Opsi 2 bila disetujui).

## 10. Galat umum
| Gejala | Penyebab | Perbaikan |
|---|---|---|
| Halaman putih / 500 | `.env` hilang, APP_KEY kosong, izin `storage`, versi PHP | Baca `laravel/storage/logs/laravel.log`; periksa `.env` dan chmod |
| `Failed opening required ... vendor/autoload.php` | Jalur pada `index.php` salah, `vendor/` belum lengkap | Periksa tiga jalur `'/../laravel/...` dan unggahan `vendor/` |
| "Your PHP version ... does not satisfy" | PHP server lebih rendah dari syarat | Minta penyedia menaikkan/mengganti versi PHP |
| `/katalog` 404 tetapi `/` terbuka | `.htaccess` tidak terunggah atau rewrite mati | Pastikan `public_html/.htaccess` ada (berkas tersembunyi); minta aktifkan mod_rewrite |
| CSS/JS tidak tampil | `APP_URL` salah atau `assets/` tidak terunggah | Samakan `APP_URL`, unggah ulang `public_html/assets` |
| `SQLSTATE[HY000] [1045]/[2002]` | Kredensial atau host database salah | Periksa nilai `DB_*` dari penyedia |
| `Table ... doesn't exist` | Database belum diimpor | Lakukan bagian 6 |
| Foto produk tidak tampil | Baris `usePublicPath` hilang atau izin `uploads/products` | Bagian 3 dan 7 |
| 419 Page Expired setelah login | Cookie sesi tidak tersimpan (HTTPS/domain) | `SESSION_SECURE_COOKIE=true` hanya jika HTTPS; hapus cookie; periksa `APP_URL` |

## 11. Daftar keamanan
- `APP_DEBUG=false`, `.env` tidak dapat diakses publik, `info.php` dihapus.
- Kata sandi admin kuat dan unik; ganti setelah impor.
- Cadangkan database dan folder `public_html/uploads` secara berkala.
- Jangan membagikan data FTP/DB lewat repository.
