@extends('layouts.dashboard')
@section('title','Ongoing Rentals')
@section('page_label','Ongoing Rentals')
@section('content')
<x-dash.page-header title="Ongoing Rentals" description="Penyewaan yang sudah diambil pelanggan dan belum tercatat kembali." />
<x-dash.panel flush><x-dash.table id="tblOngoing" caption="Daftar penyewaan berjalan" placeholder="Cari invoice, pelanggan, atau kode unit…" :rows="$rows" :columns="[['key'=>'invoice','label'=>'Invoice','type'=>'mono'],['key'=>'stack','label'=>'Pelanggan & perangkat','type'=>'stack'],['key'=>'period','label'=>'Periode sewa'],['key'=>'due','label'=>'Sisa waktu'],['key'=>'rental','label'=>'Penyewaan','type'=>'status','group'=>'rental'],['type'=>'actions']]" empty="Belum ada penyewaan yang sedang berjalan." /></x-dash.panel>
@endsection
