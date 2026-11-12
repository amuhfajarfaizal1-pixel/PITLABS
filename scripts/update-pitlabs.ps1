<#
.SYNOPSIS
  Menerapkan paket pembaruan PITLABS (zip) ke proyek Laravel Anda TANPA composer create-project.

.USAGE
  Dari folder proyek (yang berisi file "artisan"):
    powershell -ExecutionPolicy Bypass -File scripts\update-pitlabs.ps1 -Zip "C:\Users\Anda\Downloads\pitlabs-xxx.zip"
    powershell -ExecutionPolicy Bypass -File scripts\update-pitlabs.ps1 -Zip "...zip" -Migrate

  -Migrate  juga menjalankan "php artisan migrate" (gunakan jika paket menambah migration).

  Skrip ini TIDAK menyentuh: .env, vendor, storage, database, dan folder .git.
  Composer hanya perlu dijalankan bila composer.json berubah (jarang).
#>
param(
    [Parameter(Mandatory = $true)][string]$Zip,
    [switch]$Migrate
)

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot

if (-not (Test-Path (Join-Path $root 'artisan'))) {
    throw "Folder proyek Laravel tidak ditemukan: file 'artisan' tidak ada di $root. Letakkan folder scripts/ di dalam proyek."
}
if (-not (Test-Path $Zip)) { throw "File zip tidak ditemukan: $Zip" }

$tmp = Join-Path ([System.IO.Path]::GetTempPath()) ('pitlabs_' + [guid]::NewGuid().ToString('N'))
try {
    Expand-Archive -Path $Zip -DestinationPath $tmp -Force

    # Lindungi berkas lokal bila tidak sengaja ikut di dalam zip.
    foreach ($protected in '.env', 'vendor', 'storage', '.git') {
        $p = Join-Path $tmp $protected
        if (Test-Path $p) { Remove-Item $p -Recurse -Force }
    }

    # Salin semua (termasuk berkas tersembunyi seperti .gitignore), timpa yang sama.
    Get-ChildItem -Path $tmp -Force | Copy-Item -Destination $root -Recurse -Force

    # Hapus berkas lama yang sudah digantikan pada fase sebelumnya.
    $obsolete = @(
        'app/Support/DemoAuth.php',
        'app/Http/Middleware/DemoRole.php',
        'app/Support/MockCatalog.php'
    )
    foreach ($rel in $obsolete) {
        $f = Join-Path $root $rel
        if (Test-Path $f) { Remove-Item $f -Force; Write-Host "Dihapus (usang): $rel" }
    }
}
finally {
    if (Test-Path $tmp) { Remove-Item $tmp -Recurse -Force }
}

# Perintah native (php) boleh menulis peringatan tanpa menghentikan skrip; cek kode keluarnya.
$ErrorActionPreference = 'Continue'
Push-Location $root
try {
    php artisan optimize:clear
    if ($LASTEXITCODE -ne 0) { throw "php artisan optimize:clear gagal. Pastikan 'php -v' berjalan dan vendor/ sudah terpasang (composer install)." }
    if ($Migrate) {
        php artisan migrate
        if ($LASTEXITCODE -ne 0) { throw "php artisan migrate gagal. Periksa koneksi database di .env dan pastikan MySQL menyala." }
    }
    Write-Host "`nPembaruan diterapkan. Jalankan: php artisan test   lalu   php artisan serve" -ForegroundColor Green
}
finally { Pop-Location }
