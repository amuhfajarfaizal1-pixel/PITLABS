@extends('layouts.dashboard')
@section('title', 'Manajemen Produk')
@section('page_label', 'Produk')

@section('content')
<x-dash.page-header title="Manajemen produk" description="Tujuh produk resmi PITLABS. Harga, spesifikasi, dan foto diisi setelah datanya tersedia dari mitra.">
    <x-slot:actions><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah produk</button></x-slot:actions>
</x-dash.page-header>

<x-dash.panel flush>
    <x-dash.table id="tblProd" caption="Daftar produk" placeholder="Cari produk…" :rows="$rows" :columns="[
        ['key'=>'stack','label'=>'Produk','type'=>'stack'],
        ['key'=>'type','label'=>'Jenis'],
        ['key'=>'units','label'=>'Unit'],
        ['key'=>'rental','label'=>'Sewa'],
        ['key'=>'sale','label'=>'Jual (Shopee)'],
        ['key'=>'price','label'=>'Harga','type'=>'pending','pending'=>'product.price'],
        ['type'=>'actions'],
    ]" />
</x-dash.panel>

<x-modal id="productModal" title="Tambah produk" size="lg">
    <form id="productForm" data-pit-validate data-pit-simulate novalidate>
        <x-field name="product_name" label="Nama produk" required maxlength="180" />
        <div class="mb-3">
            <label for="product_type" class="form-label">Jenis <span class="pit-required" aria-hidden="true">*</span></label>
            <select id="product_type" class="form-select" required><option value="">Pilih jenis</option><option>Perangkat fisik</option><option>Layanan digital</option></select>
            <div id="product_type-error" class="invalid-feedback"></div>
        </div>
        <div class="d-flex gap-4 mb-3">
            <div class="form-check"><input class="form-check-input" type="checkbox" id="p_rental" checked><label class="form-check-label" for="p_rental">Disewakan</label></div>
            <div class="form-check"><input class="form-check-input" type="checkbox" id="p_sale"><label class="form-check-label" for="p_sale">Dijual via Shopee</label></div>
        </div>
        <x-field name="product_shopee" label="URL Shopee" type="url" hint="Wajib jika dijual via Shopee." />
        <div data-pit-form-status hidden tabindex="-1"><x-alert type="success" title="Form valid (simulasi)">Produk belum disimpan ke database.</x-alert></div>
    </form>
    <x-slot:footer>
        <button type="button" class="btn btn-soft" data-bs-dismiss="modal">Tutup</button>
        <button type="submit" form="productForm" class="btn btn-primary">Simpan produk</button>
    </x-slot:footer>
</x-modal>
@endsection
