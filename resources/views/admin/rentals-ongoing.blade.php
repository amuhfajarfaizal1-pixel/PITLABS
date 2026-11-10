@extends('layouts.dashboard')
@section('title', 'Ongoing Rentals')
@section('page_label', 'Ongoing Rentals')

@section('content')
<x-dash.page-header title="Ongoing Rentals" description="Perangkat yang sedang disewa, termasuk yang mendekati batas dan terlambat." />

<x-dash.panel flush>
    <x-dash.table id="tblOngoing" caption="Daftar penyewaan berjalan" placeholder="Cari invoice, pelanggan, atau kode unit…" :rows="$rows" :columns="[
        ['key'=>'invoice','label'=>'Invoice','type'=>'mono'],
        ['key'=>'stack','label'=>'Pelanggan & perangkat','type'=>'stack'],
        ['key'=>'period','label'=>'Periode sewa'],
        ['key'=>'due','label'=>'Sisa waktu'],
        ['key'=>'rental','label'=>'Penyewaan','type'=>'status','group'=>'rental'],
        ['type'=>'actions'],
    ]">
        <x-slot:filters>
            <label for="fRent" class="visually-hidden">Filter status penyewaan</label>
            <select id="fRent" class="form-select" data-pit-table-filter data-key="rental">
                <option value="">Semua status</option>
                <option value="RENTING">Sedang disewa</option>
                <option value="DUE_SOON">Mendekati batas</option>
                <option value="OVERDUE">Terlambat</option>
            </select>
        </x-slot:filters>
    </x-dash.table>
</x-dash.panel>
@endsection
