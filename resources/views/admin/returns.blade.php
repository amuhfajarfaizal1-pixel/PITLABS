@extends('layouts.dashboard')
@section('title','Pengembalian')
@section('page_label','Pengembalian')
@section('content')
<x-dash.page-header title="Manajemen pengembalian" description="Terima perangkat secara fisik. Setelah diterima, perangkat masuk ke antrean pemeriksaan kondisi." />
<x-dash.panel flush><x-dash.table id="tblReturn" caption="Penyewaan yang belum dikembalikan" placeholder="Cari invoice, pelanggan, atau kode unit…" :rows="$rows" :columns="[['key'=>'invoice','label'=>'Invoice','type'=>'mono'],['key'=>'stack','label'=>'Pelanggan & perangkat','type'=>'stack'],['key'=>'period','label'=>'Periode sewa'],['key'=>'due','label'=>'Sisa waktu'],['key'=>'rental','label'=>'Penyewaan','type'=>'status','group'=>'rental'],['type'=>'actions']]" empty="Tidak ada perangkat yang menunggu pengembalian." /></x-dash.panel>
@endsection
