@extends('layouts.dashboard')
@section('title', 'New Orders')
@section('page_label', 'New Orders')

@section('content')
<x-dash.page-header title="New Orders" description="Pesanan baru dan yang menunggu pembayaran. Status order, pembayaran, pengambilan, dan penyewaan dikelola terpisah." />

<x-dash.panel flush>
    <x-dash.table id="tblNew" caption="Daftar New Orders" placeholder="Cari invoice atau pelanggan…" :rows="$rows" :columns="[
        ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
        ['key'=>'stack','label'=>'Pelanggan & perangkat','type'=>'stack'],
        ['key'=>'period','label'=>'Periode sewa'],
        ['key'=>'method','label'=>'Metode'],
        ['key'=>'total','label'=>'Total','type'=>'money'],
        ['key'=>'order','label'=>'Pesanan','type'=>'status','group'=>'order'],
        ['key'=>'payment','label'=>'Pembayaran','type'=>'status','group'=>'payment'],
        ['type'=>'actions'],
    ]">
        <x-slot:filters>
            <label for="fPay" class="visually-hidden">Filter pembayaran</label>
            <select id="fPay" class="form-select" data-pit-table-filter data-key="payment">
                <option value="">Semua pembayaran</option>
                <option value="UNPAID">Belum dibayar</option>
                <option value="PAID">Lunas</option>
            </select>
        </x-slot:filters>
    </x-dash.table>
</x-dash.panel>
@endsection
