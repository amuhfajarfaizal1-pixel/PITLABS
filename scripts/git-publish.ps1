<#
.SYNOPSIS
  Commit semua perubahan lalu push ke GitHub dengan pengaman.

.USAGE
  powershell -ExecutionPolicy Bypass -File scripts\git-publish.ps1 -Message "Fase 5C: booking dan checkout"
  powershell -ExecutionPolicy Bypass -File scripts\git-publish.ps1 -Message "..." -Branch feature/nama-fitur

  Urutan: periksa keamanan -> add -> commit -> pull --rebase -> push.
  Pertama kali (repo belum terhubung)? Ikuti docs/GIT_WORKFLOW.md bagian "Pertama kali".
#>
param(
    [Parameter(Mandatory = $true)][string]$Message,
    [string]$Branch = 'main'
)

# Perintah native (git) memeriksa $LASTEXITCODE secara eksplisit.
$ErrorActionPreference = 'Continue'
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

function Fail($text) { Write-Host "`n$text" -ForegroundColor Red; exit 1 }

if (-not (Test-Path (Join-Path $root '.git'))) { Fail "Folder ini belum repository Git. Lihat docs/GIT_WORKFLOW.md bagian 'Pertama kali'." }
if (-not (git remote)) { Fail "Belum ada remote. Jalankan: git remote add origin https://github.com/amuhfajarfaizal1-pixel/PITLABS.git" }

# Pengaman: rahasia dan folder besar tidak boleh ikut repository.
if (git ls-files .env) { Fail ".env terlacak Git (berisi rahasia). Jalankan: git rm --cached .env" }
if (git ls-files vendor | Select-Object -First 1) { Fail "vendor/ terlacak Git. Jalankan: git rm -r --cached vendor" }
if (git ls-files pitlabs_private | Select-Object -First 1) { Write-Warning "pitlabs_private/ (PHP native lama) terlacak. Disarankan: git rm -r --cached pitlabs_private" }

# Pindah/buat cabang tujuan.
$current = (git branch --show-current)
if ($current -ne $Branch) {
    git rev-parse --verify --quiet "refs/heads/$Branch" *> $null
    if ($LASTEXITCODE -eq 0) { git checkout $Branch } else { git checkout -b $Branch }
    if ($LASTEXITCODE -ne 0) { Fail "Gagal berpindah ke cabang $Branch." }
}

git add -A
if (-not (git status --porcelain)) { Write-Host "Tidak ada perubahan untuk di-commit."; exit 0 }

git commit -m $Message
if ($LASTEXITCODE -ne 0) { Fail "Commit gagal. Jika diminta identitas: git config --global user.name ""Nama""  dan  git config --global user.email ""email@anda""" }

# Sinkronkan dulu agar push tidak ditolak (non-fast-forward) bila rekan setim sudah push.
git ls-remote --exit-code --heads origin $Branch *> $null
if ($LASTEXITCODE -eq 0) {
    git pull --rebase origin $Branch
    if ($LASTEXITCODE -ne 0) { Fail "Rebase bentrok. Selesaikan konflik lalu: git rebase --continue (atau git rebase --abort), kemudian jalankan skrip lagi." }
}

git push -u origin $Branch
if ($LASTEXITCODE -ne 0) { Fail "Push gagal. Cocokkan pesan galat dengan tabel di docs/GIT_WORKFLOW.md." }

Write-Host "`nBerhasil dikirim ke GitHub (cabang $Branch)." -ForegroundColor Green
