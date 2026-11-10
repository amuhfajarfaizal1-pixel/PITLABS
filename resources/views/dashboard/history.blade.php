@extends('layouts.dashboard')
@section('title', 'Riwayat Transaksi')
@section('page_label', 'Riwayat transaksi')

@section('content')
<x-dash.page-header title="Riwayat transaksi" description="Semua pesanan Anda beserta status pembayaran dan penyewaan. Buka invoice untuk detail dan QR pengambilan." />

<x-dash.panel flush>
    <x-dash.table id="tblHist" caption="Riwayat transaksi" placeholder="Cari invoice atau perangkat…" :rows="$rows" :columns="[
        ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
        ['key'=>'stack','label'=>'Perangkat','type'=>'stack'],
        ['key'=>'period','label'=>'Periode sewa'],
        ['key'=>'total','label'=>'Total','type'=>'money'],
        ['key'=>'payment','label'=>'Pembayaran','type'=>'status','group'=>'payment'],
        ['key'=>'pickup','label'=>'Pengambilan','type'=>'status','group'=>'pickup'],
        ['key'=>'rental','label'=>'Penyewaan','type'=>'status','group'=>'rental'],
        ['type'=>'actions'],
    ]">
        <x-slot:filters>
            <label for="fH" class="visually-hidden">Filter status penyewaan</label>
            <select id="fH" class="form-select" data-pit-table-filter data-key="rental">
                <option value="">Semua status</option>
                <option value="RENTING">Sedang disewa</option>
                <option value="NOT_STARTED">Belum dimulai</option>
                <option value="COMPLETED">Selesai</option>
            </select>
        </x-slot:filters>
    </x-dash.table>
</x-dash.panel>
@endsection
