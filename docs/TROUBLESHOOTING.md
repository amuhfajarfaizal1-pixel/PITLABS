# Troubleshooting lingkungan lokal (Windows + XAMPP)

## 1. `PHP Warning: Module "openssl" is already loaded in Unknown on line 0`

**Penyebab:** peringatan ini muncul saat PHP mencoba memuat ekstensi OpenSSL **lebih dari sekali** ketika proses dimulai ("Unknown on line 0" berarti terjadi sebelum ada skrip yang berjalan). Sumbernya adalah konfigurasi PHP di komputer, **bukan kode proyek**: tidak ada berkas proyek yang memuat atau mengatur ekstensi PHP. Peringatan tidak merusak aplikasi, tetapi mengotori output Composer dan Artisan.

Penyebab yang paling umum:
1. `extension=openssl` tertulis dua kali (tidak diberi `;`) di `php.ini`.
2. Kombinasi `extension=openssl` dan `extension=php_openssl.dll` (nama lama) di berkas yang sama.
3. Ada **lebih dari satu berkas ini** yang dibaca PHP (mis. `php.ini` ditambah berkas di folder `conf.d`/`PHP_INI_SCAN_DIR`) dan keduanya mengaktifkan OpenSSL.
4. Ada **lebih dari satu instalasi PHP** di PATH (mis. XAMPP lama 8.0 dan PHP 8.5 baru), sehingga `php` dan Composer memakai `php.ini` yang berbeda dari yang Anda edit.

**Cara mendiagnosis (PowerShell, jalankan berurutan):**
```powershell
where.exe php                 # daftar semua php.exe di PATH; yang pertama adalah yang dipakai
php -v                        # versi yang benar-benar aktif
php --ini                     # "Loaded Configuration File" = php.ini yang dibaca
                              # "Additional .ini files parsed" = berkas tambahan (jika ada)
php -m | findstr /i openssl   # harus tampil satu baris "openssl"
```
Lalu cari semua baris OpenSSL pada berkas yang ditampilkan `php --ini` (ganti path sesuai hasil):
```powershell
findstr /n /i "openssl" C:\xampp\php\php.ini
```

**Cara memperbaiki:**
1. Buka **berkas yang ditampilkan "Loaded Configuration File"** (bukan sembarang `php.ini`).
2. Pastikan hanya **satu** baris aktif: `extension=openssl`. Beri tanda `;` pada baris duplikat, mis. `;extension=openssl` atau `;extension=php_openssl.dll`.
3. Jika "Additional .ini files parsed" menampilkan berkas lain yang juga memuat OpenSSL, beri `;` di salah satunya.
4. Periksa variabel lingkungan `PHPRC` dan `PHP_INI_SCAN_DIR` (Sistem → Variabel Lingkungan). Hapus jika menunjuk ke konfigurasi PHP yang tidak dipakai.
5. Jika `where.exe php` menampilkan lebih dari satu lokasi, hapus path PHP yang lama dari PATH agar hanya PHP yang dipakai proyek yang tersisa.
6. Buka **PowerShell baru**, lalu jalankan `php -v`. Peringatan harus hilang.

Setelah perbaikan, `php -m | findstr /i openssl` tetap harus menampilkan `openssl` (ekstensi tetap aktif, hanya tidak dimuat dua kali).

## 2. `php artisan test`: `no such table: ...`
Tes memakai SQLite in-memory yang kosong. Setiap kelas tes yang menyentuh halaman/model berbasis database harus memakai trait `RefreshDatabase` agar migration dijalankan dan data diisolasi. Tes bawaan Laravel (`ExampleTest`) tidak memakainya, padahal beranda kini membaca tabel `products`.

## 3. `php artisan test`: `UNIQUE constraint failed: users.email`
Fixture tes membuat dua pengguna dengan email yang sama dalam satu tes. Constraint unik adalah perilaku yang benar dan **tidak boleh dilonggarkan**; beri email berbeda pada fixture kedua.

## 4. `Could not find driver` saat tes
Aktifkan `extension=pdo_sqlite` (dan `sqlite3`) di `php.ini` yang dibaca PHP, lalu buka terminal baru.
