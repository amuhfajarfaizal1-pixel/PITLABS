@extends('layouts.dashboard')
@section('title', 'Inventaris')
@section('page_label', 'Inventaris')

@section('content')
<x-dash.page-header title="Manajemen inventaris" description="Setiap unit fisik memiliki kode unik. Unit tidak dihapus: pilih status Dipensiunkan. Ketersediaan per tanggal dihitung dari booking, bukan dari tabel ini.">
    <x-slot:actions><a href="{{ route('admin.units.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah unit</a></x-slot:actions>
</x-dash.page-header>

<x-dash.panel flush>
    <x-dash.table id="tblInv" caption="Daftar unit rental" placeholder="Cari kode unit atau produk…" :rows="$rows" :columns="[
        ['key'=>'code','label'=>'Kode unit','type'=>'mono'],
        ['key'=>'product','label'=>'Produk'],
        ['key'=>'serial','label'=>'Nomor seri'],
        ['key'=>'condition','label'=>'Kondisi','type'=>'status','group'=>'condition'],
        ['key'=>'operational','label'=>'Operasional','type'=>'status','group'=>'operational'],
        ['type'=>'actions'],
    ]">
        <x-slot:filters>
            <label for="fInv" class="visually-hidden">Filter status operasional</label>
            <select id="fInv" class="form-select" data-pit-table-filter data-key="operational">
                <option value="">Semua status</option>
                <option value="ACTIVE">Aktif</option>
                <option value="MAINTENANCE">Perawatan</option>
                <option value="RETIRED">Dipensiunkan</option>
            </select>
        </x-slot:filters>
    </x-dash.table>
</x-dash.panel>
@endsection
