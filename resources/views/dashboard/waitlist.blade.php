@extends('layouts.dashboard')
@section('title', 'Antrean Rental')
@section('page_label', 'Antrean rental')

@section('content')
<x-dash.page-header title="Antrean rental" description="Permintaan antrean muncul saat unit bentrok dengan jadwal lain. Kami memberi tahu saat ada unit yang bebas.">
    <x-slot:actions><a href="{{ route('availability') }}" class="btn btn-primary">Cek ketersediaan</a></x-slot:actions>
</x-dash.page-header>

<x-dash.panel flush>
    <x-dash.table id="tblMyWait" :searchable="false" caption="Antrean rental saya" :rows="$rows" empty="Belum ada antrean." :columns="[
        ['key'=>'product','label'=>'Produk'],
        ['key'=>'period','label'=>'Periode'],
        ['key'=>'days','label'=>'Durasi'],
        ['key'=>'status','label'=>'Status','type'=>'status','group'=>'waitlist'],
    ]" />
</x-dash.panel>
@endsection
