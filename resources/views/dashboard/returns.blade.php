@extends('layouts.dashboard')
@section('title', 'Informasi Pengembalian')
@section('page_label', 'Informasi pengembalian')
@push('head')<link rel="stylesheet" href="{{ asset('assets/css/pages/home.css') }}">@endpush

@section('content')
<x-dash.page-header title="Informasi pengembalian" description="Pengembalian dilakukan langsung di lokasi admin. Admin memeriksa kondisi sebelum sewa dinyatakan selesai." />

<div class="dash-grid dash-grid--2 mb-4">
    <x-dash.panel title="Cara mengembalikan">
        <ol class="pit-steps" style="grid-template-columns: 1fr">
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">1</span><h3 class="pit-step__title">Kembalikan sebelum batas waktu</h3><p>Lihat batas sewa pada penyewaan aktif Anda. Keterlambatan dikenai ketentuan mitra.</p></li>
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">2</span><h3 class="pit-step__title">Datang ke lokasi admin</h3><p>Lokasi dan jam operasional: <x-pending field="terms.pickup_address" /></p></li>
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">3</span><h3 class="pit-step__title">Serahkan perangkat lengkap</h3><p>Bawa seluruh kelengkapan yang diterima saat pengambilan.</p></li>
            <li class="pit-step"><span class="pit-step__num" aria-hidden="true">4</span><h3 class="pit-step__title">Pemeriksaan oleh admin</h3><p>Status berubah menjadi "Dalam pemeriksaan". Jika tidak ada masalah, sewa selesai.</p></li>
        </ol>
        <x-alert type="info" class="mt-4" title="Denda">Denda kerusakan ditetapkan setelah pemeriksaan admin sesuai ketentuan mitra. Rincian denda tampil di dashboard Anda. Lihat <a class="pit-link" href="{{ route('guide') }}#ketentuan-sewa">ketentuan sewa dan denda</a>.</x-alert>
    </x-dash.panel>

    <x-dash.panel title="Jadwal pengembalian Anda" flush>
        <ul class="dash-list">
            @forelse ($active as $o)
                <li class="dash-list__item">
                    <div class="dash-list__main"><strong>{{ $o['product'] }} <span class="pit-mono">{{ $o['unit'] }}</span></strong><small>Batas: {{ $o['end']->locale('id')->translatedFormat('j F Y') }} · {{ $o['due_label'] }}</small></div>
                    <x-status-badge group="rental" :status="$o['rental']" />
                </li>
            @empty
                <li class="dash-list__item"><x-empty-state icon="bi-check2-all" title="Tidak ada yang perlu dikembalikan">Anda tidak memiliki penyewaan berjalan.</x-empty-state></li>
            @endforelse
        </ul>
    </x-dash.panel>
</div>
@endsection
