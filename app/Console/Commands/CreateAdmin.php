<?php

namespace App\Console\Commands;

use App\Support\AdminAccount;
use Illuminate\Console\Command;
use InvalidArgumentException;

class CreateAdmin extends Command
{
    protected $signature = 'pitlabs:create-admin
        {--name= : Nama admin}
        {--email= : Email admin}
        {--password= : Kata sandi (lebih aman dikosongkan agar ditanya, supaya tidak tersimpan di riwayat terminal)}
        {--reset : Atur ulang kata sandi admin yang sudah ada}
        {--promote : Dengan --reset, jadikan akun pelanggan yang sudah ada sebagai admin}';

    protected $description = 'Membuat (atau mengatur ulang) akun admin PITLABS secara manual';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nama admin', 'Admin PITLABS');
        $email = $this->option('email') ?: $this->ask('Email admin');
        $password = $this->option('password');

        if (! $password) {
            $password = $this->secret('Kata sandi (minimal 10 karakter)');
            if ($password !== $this->secret('Ulangi kata sandi')) {
                $this->error('Konfirmasi kata sandi tidak sama.');

                return self::FAILURE;
            }
        }

        try {
            $result = AdminAccount::createOrReset((string) $name, (string) $email, (string) $password, (bool) $this->option('reset'), (bool) $this->option('promote'));
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info($result === AdminAccount::CREATED
            ? 'Akun admin dibuat: '.mb_strtolower($email)
            : 'Kata sandi admin diatur ulang: '.mb_strtolower($email));

        return self::SUCCESS;
    }
}
