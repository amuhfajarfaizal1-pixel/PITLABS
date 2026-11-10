{{-- Tombol login pihak ketiga (Google, OAuth 2.0) + modal simulasi.
     Fase 5: tombol menjadi tautan ke route Socialite (redirect ke Google). --}}
<button type="button" class="btn btn-google w-100" data-bs-toggle="modal" data-bs-target="#googleModal">
    <i class="bi bi-google" aria-hidden="true"></i> {{ $label }}
</button>

<div class="pit-divider" role="separator" aria-label="atau"><span>atau dengan email</span></div>

<x-modal id="googleModal" title="Login Google segera aktif">
    <p class="mb-2">Masuk dan daftar dengan akun Google akan diaktifkan pada tahap berikutnya, setelah kredensial OAuth Google tersedia (Laravel Socialite, OAuth 2.0).</p>
    <p class="mb-0 text-muted-pit">Untuk sekarang, gunakan email dan kata sandi di bawah.</p>

    <x-slot:footer>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
    </x-slot:footer>
</x-modal>
