@extends('layouts.dashboard')
@section('title', 'Pengelolaan Denda')
@section('page_label', 'Pengelolaan denda')

@section('content')
<x-dash.page-header title="Pengelolaan denda" description="Denda dicatat terpisah dari biaya sewa, berdasarkan keputusan dan catatan pemeriksaan admin." />

<x-alert type="warning" class="mb-4" title="Nominal denda belum ditetapkan">Contoh dan nominal per kategori kerusakan serta tarif keterlambatan menunggu pengesahan mitra. Sistem tidak mengestimasi tingkat kerusakan otomatis.</x-alert>

<x-dash.panel flush>
    <x-dash.table id="tblFine" caption="Daftar denda" placeholder="Cari invoice atau pelanggan…" :rows="$rows" :columns="[
        ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
        ['key'=>'customer','label'=>'Pelanggan'],
        ['key'=>'kind','label'=>'Jenis'],
        ['key'=>'basis','label'=>'Dasar'],
        ['key'=>'amount','label'=>'Nilai denda','type'=>'money','pending'=>'fine.amount'],
        ['key'=>'decision','label'=>'Keputusan'],
    ]" />
</x-dash.panel>
@endsection
