@extends('layouts.dashboard')
@section('title', 'Inventaris')
@section('page_label', 'Inventaris')

@section('content')
<x-dash.page-header title="Manajemen inventaris" description="Setiap unit fisik memiliki kode unik. Status di sini hanya gambaran hari ini; ketersediaan sebenarnya dihitung dari kalender booking.">
    <x-slot:actions><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unitModal"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah unit</button></x-slot:actions>
</x-dash.page-header>

<x-dash.panel flush>
    <x-dash.table id="tblInv" caption="Daftar unit rental" placeholder="Cari kode unit atau produk…" :rows="$rows" :columns="[
        ['key'=>'code','label'=>'Kode unit','type'=>'mono'],
        ['key'=>'product','label'=>'Produk'],
        ['key'=>'status','label'=>'Status hari ini','type'=>'status','group'=>'unit'],
    ]">
        <x-slot:filters>
            <label for="fInv" class="visually-hidden">Filter status unit</label>
            <select id="fInv" class="form-select" data-pit-table-filter data-key="status">
                <option value="">Semua status</option>
                <option value="AVAILABLE">Tersedia</option>
                <option value="RESERVED">Dipesan</option>
                <option value="RENTED">Disewa</option>
            </select>
        </x-slot:filters>
    </x-dash.table>
</x-dash.panel>

<x-modal id="unitModal" title="Tambah unit rental">
    <form id="unitForm" data-pit-validate data-pit-simulate novalidate>
        <div class="mb-3">
            <label for="unit_product" class="form-label">Produk <span class="pit-required" aria-hidden="true">*</span></label>
            <select id="unit_product" class="form-select" required>
                <option value="">Pilih produk fisik</option>
                @foreach ($catalog as $p)<option>{{ $p['name'] }}</option>@endforeach
            </select>
            <div id="unit_product-error" class="invalid-feedback"></div>
        </div>
        <x-field name="unit_code" label="Kode unit" required pattern="[A-Z]{3}-[0-9]{3}" data-pattern-msg="Format kode: tiga huruf besar, tanda minus, tiga angka. Contoh: JON-004" hint="Harus unik. Contoh: JON-004" />
        <div data-pit-form-status hidden tabindex="-1"><x-alert type="success" title="Form valid (simulasi)">Unit belum disimpan ke database.</x-alert></div>
    </form>
    <x-slot:footer>
        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Tutup</button>
        <button type="submit" form="unitForm" class="btn btn-primary">Simpan unit</button>
    </x-slot:footer>
</x-modal>
@endsection
