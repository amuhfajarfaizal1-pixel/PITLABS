@extends('layouts.dashboard')
@section('title', 'Pelanggan')
@section('page_label', 'Pelanggan')

@section('content')
<x-dash.page-header title="Manajemen pelanggan" description="Daftar akun pelanggan. Akun nonaktif tidak dapat membuat pesanan baru." />

<x-dash.panel flush>
    <x-dash.table id="tblCust" caption="Daftar pelanggan" placeholder="Cari nama atau email…" :rows="$rows" :columns="[
        ['key'=>'stack','label'=>'Pelanggan','type'=>'stack'],
        ['key'=>'phone','label'=>'WhatsApp','type'=>'text'],
        ['key'=>'joined','label'=>'Tanggal daftar'],
        ['key'=>'account','label'=>'Status','type'=>'status','group'=>'account'],
        ['type'=>'actions'],
    ]">
        <x-slot:filters>
            <label for="fCust" class="visually-hidden">Filter status akun</label>
            <select id="fCust" class="form-select" data-pit-table-filter data-key="account">
                <option value="">Semua status</option><option value="ACTIVE">Aktif</option><option value="INACTIVE">Nonaktif</option>
            </select>
        </x-slot:filters>
    </x-dash.table>
</x-dash.panel>
@endsection
