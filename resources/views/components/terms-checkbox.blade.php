{{--
    Checkbox persetujuan ketentuan sewa. Teks ketentuan berupa tautan biru yang
    membuka bagian #ketentuan-sewa di tab baru, sehingga isian form tidak hilang.
    Dipakai di registrasi/checkout rental (wajib dicentang — PRD FR-06).
    <x-terms-checkbox id="terms" />
--}}
@props(['id' => 'terms', 'name' => 'terms_accepted'])

<div class="form-check mb-4">
    <input class="form-check-input" type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" required>
    <label class="form-check-label" for="{{ $id }}">
        Saya menyetujui
        <a class="pit-link" href="{{ \App\Support\Pit::termsUrl() }}" target="_blank" rel="noopener">ketentuan sewa dan denda kerusakan/kehilangan<i class="bi bi-box-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> (terbuka di tab baru)</span></a>
    </label>
    <div id="{{ $id }}-error" class="invalid-feedback"></div>
</div>
