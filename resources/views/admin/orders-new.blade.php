@extends('layouts.dashboard')
@section('title','New Orders')
@section('page_label','New Orders')
@section('content')
<x-dash.page-header title="New Orders" description="Pesanan yang tercatat di database. Verifikasi pembayaran dilakukan melalui halaman pembayaran; status lain tetap terpisah." />
<x-dash.panel flush><x-dash.table id="tblNew" caption="Daftar pesanan baru" placeholder="Cari invoice atau pelanggan…" :rows="$rows" :columns="[['key'=>'invoice','label'=>'Invoice','type'=>'mono'],['key'=>'stack','label'=>'Pelanggan & perangkat','type'=>'stack'],['key'=>'period','label'=>'Periode sewa'],['key'=>'method','label'=>'Metode'],['key'=>'total','label'=>'Total','type'=>'money'],['key'=>'order','label'=>'Pesanan','type'=>'status','group'=>'order'],['key'=>'payment','label'=>'Pembayaran','type'=>'status','group'=>'payment'],['type'=>'actions']]" empty="Belum ada pesanan baru di database." /></x-dash.panel>
@endsection
