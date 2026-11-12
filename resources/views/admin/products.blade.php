@extends('layouts.dashboard')
@section('title', 'Manajemen Produk')
@section('page_label', 'Produk')

@section('content')
<x-dash.page-header title="Manajemen produk" description="Produk yang aktif tampil di katalog. Produk tidak dihapus, hanya dinonaktifkan, supaya riwayat pesanan tetap utuh.">
    <x-slot:actions><a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah produk</a></x-slot:actions>
</x-dash.page-header>

<x-dash.panel flush>
    <x-dash.table id="tblProd" caption="Daftar produk" placeholder="Cari produk…" :rows="$rows" :columns="[
        ['key'=>'stack','label'=>'Produk','type'=>'stack'],
        ['key'=>'type','label'=>'Jenis'],
        ['key'=>'units','label'=>'Unit'],
        ['key'=>'rental','label'=>'Sewa'],
        ['key'=>'sale','label'=>'Jual (Shopee)'],
        ['key'=>'rate','label'=>'Harga sewa/hari','type'=>'money','pending'=>'product.rental_daily_rate'],
        ['key'=>'status','label'=>'Status','type'=>'status','group'=>'account'],
        ['type'=>'actions'],
    ]">
        <x-slot:filters>
            <label for="fProd" class="visually-hidden">Filter status produk</label>
            <select id="fProd" class="form-select" data-pit-table-filter data-key="status">
                <option value="">Semua status</option><option value="ACTIVE">Aktif</option><option value="INACTIVE">Nonaktif</option>
            </select>
        </x-slot:filters>
    </x-dash.table>
</x-dash.panel>
@endsection
