@extends('layouts.dashboard')
@section('title', 'Antrean Rental')
@section('page_label', 'Antrean rental')

@section('content')
<x-dash.page-header title="Antrean rental" description="Permintaan yang masuk saat unit bentrok dengan jadwal lain. Antrean bukan jaminan unit." />

<x-dash.panel flush>
    <x-dash.table id="tblWait" caption="Daftar antrean rental" placeholder="Cari pelanggan atau produk…" :rows="$rows" :columns="[
        ['key'=>'customer','label'=>'Pelanggan'],
        ['key'=>'product','label'=>'Produk'],
        ['key'=>'period','label'=>'Periode'],
        ['key'=>'days','label'=>'Durasi'],
        ['key'=>'status','label'=>'Status','type'=>'status','group'=>'waitlist'],
    ]" />
</x-dash.panel>
@endsection
