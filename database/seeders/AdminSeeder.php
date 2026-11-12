<?php

namespace Database\Seeders;

use App\Support\AdminAccount;
use Illuminate\Database\Seeder;
use InvalidArgumentException;

/*
| SEEDER MANUAL — TIDAK ikut `php artisan migrate --seed`.
| Jalankan hanya saat ingin membuat admin dari konfigurasi lokal:
|     php artisan db:seed --class=AdminSeeder
| Data diambil dari .env (ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD) lewat config/pitlabs.php.
| Kata sandi sengaja TIDAK ditulis di berkas ini: repository bersifat publik di GitHub.
| Setelah admin dibuat, hapus ADMIN_PASSWORD dari .env.
*/
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $c = config('pitlabs.admin_seed');

        if (empty($c['email']) || empty($c['password'])) {
            $this->command?->error('Isi ADMIN_EMAIL dan ADMIN_PASSWORD (minimal 10 karakter) di .env, lalu jalankan lagi. Tidak ada akun yang dibuat.');

            return;
        }

        try {
            $result = AdminAccount::createOrReset($c['name'] ?: 'Admin PITLABS', $c['email'], $c['password']);
            $this->command?->info($result === AdminAccount::CREATED ? "Akun admin dibuat: {$c['email']}" : "Akun admin diperbarui: {$c['email']}");
        } catch (InvalidArgumentException $e) {
            $this->command?->error($e->getMessage());
        }
    }
}
