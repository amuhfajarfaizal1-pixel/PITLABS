<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

/**
 * Pembuatan akun admin SECARA MANUAL (perintah Artisan atau seeder khusus).
 * Tidak dipanggil otomatis oleh `migrate --seed`. Satu-satunya jalur membuat ADMIN selain database.
 */
class AdminAccount
{
    public const CREATED = 'created';
    public const RESET = 'reset';

    /**
     * @throws InvalidArgumentException dengan pesan yang dapat ditampilkan ke operator
     */
    public static function createOrReset(string $name, string $email, string $password, bool $reset = false, bool $promote = false): string
    {
        $validator = Validator::make(compact('name', 'email', 'password'), [
            'name'     => ['required', 'string', 'min:3', 'max:120'],
            'email'    => ['required', 'email:rfc', 'max:190'],
            'password' => ['required', 'string', 'min:10', 'max:255'],
        ], [
            'name.required'     => 'Nama admin wajib diisi.',
            'name.min'          => 'Nama admin minimal 3 karakter.',
            'email.required'    => 'Email admin wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 10 karakter.',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors()->first());
        }

        $email = mb_strtolower(trim($email));
        $user = User::where('email', $email)->first();

        if ($user) {
            if (! $reset) {
                throw new InvalidArgumentException("Email {$email} sudah terdaftar. Tambahkan --reset untuk mengatur ulang kata sandi admin tersebut.");
            }
            if ($user->role !== User::ROLE_ADMIN && ! $promote) {
                throw new InvalidArgumentException("{$email} adalah akun pelanggan. Tambahkan --promote jika memang ingin dijadikan admin.");
            }

            $user->forceFill(['password' => $password, 'role' => User::ROLE_ADMIN, 'is_active' => true])->save();

            return self::RESET;
        }

        User::unguarded(fn () => User::create([
            'name'              => trim($name),
            'email'             => $email,
            'password'          => $password,
            'role'              => User::ROLE_ADMIN,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]));

        return self::CREATED;
    }
}
