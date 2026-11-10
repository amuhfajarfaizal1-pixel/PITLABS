<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/*
| Membuat akun admin (dan akun pelanggan contoh khusus environment local).
| Kata sandi diambil dari .env (ADMIN_PASSWORD / DEMO_CUSTOMER_PASSWORD);
| jika kosong, dibuat acak dan DITAMPILKAN SEKALI di terminal. Tidak ada kata sandi bawaan di kode.
| Akun yang sudah ada tidak diubah.
*/
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->make('Admin PITLABS', env('ADMIN_EMAIL', 'admin@pitlabs.test'), env('ADMIN_PASSWORD'), User::ROLE_ADMIN);

        if (app()->environment('local')) {
            $this->make('Pelanggan Contoh', 'pelanggan@pitlabs.test', env('DEMO_CUSTOMER_PASSWORD'), User::ROLE_CUSTOMER);
        }
    }

    private function make(string $name, string $email, ?string $password, string $role): void
    {
        if (User::where('email', $email)->exists()) {
            $this->command?->info("Akun {$email} sudah ada, dilewati.");

            return;
        }

        $plain = $password ?: Str::password(16, symbols: false);

        User::unguarded(fn () => User::create([
            'name' => $name,
            'email' => $email,
            'password' => $plain,
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ]));

        $this->command?->warn("Akun {$role} dibuat: {$email}".($password ? '' : "  kata sandi: {$plain}  (catat sekarang, tidak ditampilkan lagi)"));
    }
}
