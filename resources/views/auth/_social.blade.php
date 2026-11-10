{{-- Tombol login pihak ketiga (Google, OAuth 2.0) + modal simulasi.
     Fase 5: tombol menjadi tautan ke route Socialite (redirect ke Google). --}}
<button type="button" class="btn btn-google w-100" data-bs-toggle="modal" data-bs-target="#googleModal">
    <i class="bi bi-google" aria-hidden="true"></i> {{ $label }}
</button>

<div class="pit-divider" role="separator" aria-label="atau"><span>atau dengan email</span></div>

<x-modal id="googleModal" title="Masuk dengan Google">
    <p class="mb-2">Login Google belum tersambung ke akun Google sungguhan. Tombol ini akan memakai OAuth 2.0 (Laravel Socialite) pada Fase 5.</p>
    <p class="mb-0 text-muted-pit">Untuk mencoba alurnya sekarang, lanjutkan sebagai akun Google demo. Tidak ada data yang dikirim ke Google.</p>

    <x-slot:footer>
        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Batal</button>
        <form method="POST" action="{{ route('auth.google.demo') }}">
            @csrf
            <input type="hidden" name="kembali" value="{{ $kembali }}">
            <button type="submit" class="btn btn-primary">Lanjutkan sebagai akun demo</button>
        </form>
    </x-slot:footer>
</x-modal>
