@extends('layouts.dashboard')
@section('title', 'Profil')
@section('page_label', 'Profil')

@section('content')
<x-dash.page-header title="Profil" description="Data akun Anda. Nama dan nomor WhatsApp dipakai pada invoice dan verifikasi pengambilan." />

<div class="dash-grid dash-grid--even">
    <x-dash.panel title="Data akun">
        <form data-pit-validate data-pit-simulate novalidate>
            <x-field name="name" label="Nama lengkap" required :value="$user['name']" minlength="3" maxlength="120" autocomplete="name" />
            <x-field name="email" label="Email" type="email" required :value="$user['email']" autocomplete="email" />
            <x-field name="phone" label="Nomor WhatsApp" type="tel" pattern="(\+62|62|0)8[0-9]{8,12}" data-pattern-msg="Nomor WhatsApp tidak valid. Contoh: 081234567890" hint="Contoh: 081234567890" />
            <button type="submit" class="btn btn-primary">Simpan perubahan</button>
            <div data-pit-form-status hidden tabindex="-1" class="mt-3"><x-alert type="success" title="Tersimpan (simulasi)">Perubahan belum disimpan ke database.</x-alert></div>
        </form>
    </x-dash.panel>

    <x-dash.panel title="Ubah kata sandi">
        @if (($user['provider'] ?? '') === 'google')
            <x-alert type="info">Anda masuk dengan Google, jadi tidak ada kata sandi PITLABS yang perlu diubah.</x-alert>
        @else
            <form data-pit-validate data-pit-simulate novalidate>
                <x-field name="current_password" label="Kata sandi saat ini" type="password" required autocomplete="current-password" />
                <x-field name="new_password" label="Kata sandi baru" type="password" required minlength="10" autocomplete="new-password" hint="Minimal 10 karakter." />
                <x-field name="new_password_confirmation" label="Ulangi kata sandi baru" type="password" required autocomplete="new-password" data-match="#field-new_password" data-msg-match="Konfirmasi kata sandi tidak sama." />
                <button type="submit" class="btn btn-outline-primary">Ubah kata sandi</button>
                <div data-pit-form-status hidden tabindex="-1" class="mt-3"><x-alert type="success" title="Tersimpan (simulasi)">Kata sandi belum diubah di database.</x-alert></div>
            </form>
        @endif
    </x-dash.panel>
</div>
@endsection
