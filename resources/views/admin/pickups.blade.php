@extends('layouts.dashboard')
@section('title', 'Verifikasi Pengambilan')
@section('page_label', 'Verifikasi pengambilan')

@section('content')
<x-dash.page-header title="Verifikasi pengambilan" description="Pesanan lunas yang siap diserahkan. Periksa identitas pelanggan dan keabsahan invoice sebelum mencatat pengambilan.">
    <x-slot:actions><a href="{{ route('admin.scanner') }}" class="btn btn-primary"><i class="bi bi-qr-code-scan" aria-hidden="true"></i> Scan QR invoice</a></x-slot:actions>
</x-dash.page-header>

<x-dash.panel flush>
    <x-dash.table id="tblPick" caption="Pesanan siap diambil" placeholder="Cari invoice atau pelanggan…" :rows="$rows" :columns="[
        ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
        ['key'=>'stack','label'=>'Pelanggan & perangkat','type'=>'stack'],
        ['key'=>'period','label'=>'Periode sewa'],
        ['key'=>'payment','label'=>'Pembayaran','type'=>'status','group'=>'payment'],
        ['key'=>'pickup','label'=>'Pengambilan','type'=>'status','group'=>'pickup'],
        ['type'=>'actions'],
    ]" />
</x-dash.panel>
@endsection
