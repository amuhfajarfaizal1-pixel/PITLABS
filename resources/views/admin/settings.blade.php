@extends('layouts.dashboard')
@section('title', 'Pengaturan')
@section('page_label', 'Pengaturan')

@section('content')
<x-dash.page-header title="Pengaturan" description="Nilai yang ditetapkan mitra. Kolom yang dikosongkan tampil sebagai &quot;Informasi belum tersedia&quot; di situs dan tidak dipakai sebagai batas." />

<div class="row"><div class="col-xl-7">
<form method="POST" action="{{ route('admin.settings.update') }}" data-pit-validate novalidate>
    @csrf @method('PUT')

    <x-dash.panel title="Durasi sewa" subtitle="Dipakai saat pelanggan memesan dan di halaman ketentuan">
        <div class="row g-3">
            <div class="col-sm-6"><x-field name="min_days" label="Minimum (hari)" type="number" min="1" max="365" inputmode="numeric" :value="$values['min_days']" hint="Kosong = 1 hari." /></div>
            <div class="col-sm-6"><x-field name="max_days" label="Maksimum (hari)" type="number" min="1" max="365" inputmode="numeric" :value="$values['max_days']" hint="Kosong = batas sistem 90 hari per pemesanan." /></div>
        </div>
    </x-dash.panel>

    <x-dash.panel title="Lokasi pengambilan dan pengembalian" class="mt-3">
        <x-field name="pickup_address" label="Alamat" maxlength="300" :value="$values['pickup_address']" hint="Tampil di invoice, footer, dan halaman ketentuan." />
        <x-field name="pickup_hours" label="Jam operasional" maxlength="120" :value="$values['pickup_hours']" hint="Contoh: Senin–Jumat, 09.00–17.00 WITA." />
    </x-dash.panel>

    <button type="submit" class="btn btn-primary mt-3">Simpan pengaturan</button>
</form>
</div></div>
@endsection
