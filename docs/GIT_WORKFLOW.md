# Alur Git dan Pembaruan PITLABS

Repository: https://github.com/amuhfajarfaizal1-pixel/PITLABS

## Prinsip
- **Folder proyek Laravel Anda adalah repository Git-nya.** Tidak perlu `composer create-project` lagi setiap pembaruan.
- `vendor/`, `.env`, dan `storage/` **tidak masuk Git** (lihat `.gitignore`). `vendor/` tetap ada di komputer Anda, jadi Composer hanya dijalankan **sekali per komputer** atau saat `composer.json` berubah.
- Pembaruan dari AI/rekan berupa zip: diterapkan dengan satu skrip, lalu dikirim ke GitHub dengan satu skrip.

## A. Alur pembaruan sehari-hari (tanpa Composer)
```powershell
cd C:\path\ke\pitlabs                      # folder yang berisi file "artisan"

# 1) Terapkan paket zip (menimpa file, menghapus berkas usang, membersihkan cache)
powershell -ExecutionPolicy Bypass -File scripts\update-pitlabs.ps1 -Zip "C:\Users\Anda\Downloads\pitlabs-xxx.zip"
#    tambahkan -Migrate bila paket menambah migration baru

# 2) Uji
php artisan test

# 3) Kirim ke GitHub (commit + sinkron + push, dengan pengaman .env/vendor)
powershell -ExecutionPolicy Bypass -File scripts\git-publish.ps1 -Message "Fase 5C: booking dan checkout" -Branch laravel
```
Paket pertama yang membawa folder `scripts/` harus diekstrak manual sekali (timpa semua), setelah itu skrip tersedia.

## B. Pertama kali: menghubungkan proyek Laravel ke repository
Repository saat ini masih berisi versi **PHP native** lama. Mencampur keduanya di satu cabang akan menimbulkan konflik (riwayat berbeda dan struktur folder berbeda), dan itulah penyebab push sering ditolak.

### Pilihan 1 (disarankan, tidak menghapus apa pun): cabang baru `laravel`
```powershell
cd C:\path\ke\pitlabs
git init -b laravel
git remote add origin https://github.com/amuhfajarfaizal1-pixel/PITLABS.git
git add -A
git status                       # PASTIKAN .env dan vendor/ TIDAK ada di daftar
git commit -m "Migrasi PITLABS ke Laravel"
git push -u origin laravel
```
Lalu di GitHub: **Settings → Branches → Default branch** → pilih `laravel`. Versi lama tetap aman di cabang `main`. Pada pembaruan berikutnya pakai `-Branch laravel`.

### Pilihan 2 (mengganti isi `main`): hanya jika tim sepakat
```powershell
# a. cadangkan versi lama dulu
git clone https://github.com/amuhfajarfaizal1-pixel/PITLABS.git PITLABS-lama
cd PITLABS-lama ; git branch legacy-native ; git push origin legacy-native ; cd ..

# b. di folder proyek Laravel
cd C:\path\ke\pitlabs
git init -b main
git remote add origin https://github.com/amuhfajarfaizal1-pixel/PITLABS.git
git add -A
git commit -m "Migrasi PITLABS ke Laravel"
git fetch origin
git push -u origin main --force-with-lease
```
Ini **menimpa** `main`; versi lama tersimpan di `legacy-native`. Rekan setim harus clone ulang (`git clone`) atau `git fetch origin` lalu `git reset --hard origin/main` setelah mencadangkan pekerjaan lokalnya.

### Rahasia yang pernah ter-commit
Jika repository lama pernah memuat kata sandi (mis. `pitlabs_private/config/config.php`), ganti kata sandi tersebut. Menghapus berkas dari commit baru tidak menghapusnya dari riwayat lama.

## C. Komputer atau rekan baru
```powershell
git clone https://github.com/amuhfajarfaizal1-pixel/PITLABS.git
cd PITLABS
git checkout laravel                # atau cabang default yang disepakati
composer install                    # sekali ini saja
copy .env.example .env
php artisan key:generate
# isi DB_DATABASE=pitlabs dan kredensial MySQL di .env, buat database pitlabs di phpMyAdmin
php artisan migrate --seed
php artisan serve
```

## D. Kerja tim (nilai kontribusi PjBL)
- Satu cabang per fitur: `git checkout -b feature/booking`. Commit dengan **akun masing-masing** supaya kontribusi tercatat.
- Sebelum push: `git pull --rebase origin <cabang>` (skrip melakukannya otomatis).
- Gabungkan lewat Pull Request, ditinjau anggota lain.

## E. Galat umum saat push
| Pesan | Penyebab | Perbaikan |
|---|---|---|
| `fatal: not a git repository` | Folder belum di-`git init` atau salah folder | `cd` ke folder berisi `artisan`, lalu bagian B |
| `error: src refspec main does not match any` | Belum ada commit, atau nama cabang `master` | `git add -A ; git commit -m "..."` atau `git branch -M main` |
| `! [rejected] ... (fetch first)` / `non-fast-forward` | Remote punya commit yang belum Anda miliki | `git pull --rebase origin <cabang>` lalu push lagi |
| `refusing to merge unrelated histories` | Menggabungkan proyek lama (native) dengan Laravel | Jangan digabung. Pakai bagian B (cabang baru) |
| `Authentication failed` / `Support for password authentication was removed` | GitHub tidak menerima kata sandi akun | Login lewat jendela browser dari Git Credential Manager, atau pakai Personal Access Token sebagai kata sandi, atau `gh auth login` |
| `Permission denied (publickey)` | Remote memakai SSH tanpa kunci | Ganti ke HTTPS: `git remote set-url origin https://github.com/amuhfajarfaizal1-pixel/PITLABS.git` |
| `remote: Permission to ... denied to <user>` | Akun bukan kolaborator repo | Pemilik repo: Settings → Collaborators → undang akun |
| `warning: LF will be replaced by CRLF` | Perbedaan akhir baris Windows | Aman diabaikan; `.gitattributes` menyeragamkan |
| `fatal: detected dubious ownership` | Folder dimiliki akun Windows lain | `git config --global --add safe.directory C:/path/ke/pitlabs` |
| Push sangat lama / ukuran besar | `vendor/` atau `node_modules/` ikut ter-commit | `git rm -r --cached vendor` lalu commit ulang |
| `.env` muncul di `git status` | `.gitignore` tidak terpasang atau `.env` pernah terlacak | `git rm --cached .env` lalu commit |
| `Author identity unknown` | Identitas Git belum diatur | `git config --global user.name "Nama"` dan `git config --global user.email "email@anda"` |

Jika galat Anda tidak ada di tabel, jalankan dan kirim hasil perintah ini:
```powershell
git status ; git remote -v ; git branch -a ; git log --oneline -3
```
