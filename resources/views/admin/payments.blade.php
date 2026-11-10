@extends('layouts.dashboard')
@section('title', 'Verifikasi Pembayaran')
@section('page_label', 'Verifikasi pembayaran')

@section('content')
<x-dash.page-header title="Verifikasi pembayaran" description="Pembayaran COD dikonfirmasi manual setelah uang diterima. Pembayaran online hanya lunas setelah webhook gateway tervalidasi, bukan dari pengalihan browser." />

<x-alert type="info" class="mb-4" title="Lunas tidak berarti sudah diambil">Menandai pembayaran lunas tidak mengubah status pengambilan atau penyewaan.</x-alert>

<x-dash.panel flush>
    <x-dash.table id="tblPay" caption="Pesanan belum dibayar" placeholder="Cari invoice atau pelanggan…" :rows="$rows" :columns="[
        ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
        ['key'=>'stack','label'=>'Pelanggan & perangkat','type'=>'stack'],
        ['key'=>'method','label'=>'Metode'],
        ['key'=>'total','label'=>'Total','type'=>'money'],
        ['key'=>'payment','label'=>'Pembayaran','type'=>'status','group'=>'payment'],
        ['key'=>'note','label'=>'Catatan'],
        ['type'=>'actions'],
    ]" />
</x-dash.panel>
@endsection
