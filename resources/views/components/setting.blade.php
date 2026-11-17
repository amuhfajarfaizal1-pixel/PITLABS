{{--
    Menampilkan nilai pengaturan admin; jika belum diisi tampil "Informasi belum tersedia".
    <x-setting key="contact.pickup_address" field="terms.pickup_address" />
--}}
@props(['key', 'field' => null, 'dark' => false])
@php $value = \App\Models\Setting::value($key); @endphp
@if ($value !== null){{ $value }}@else<x-pending :field="$field ?? $key" :dark="$dark" />@endif
