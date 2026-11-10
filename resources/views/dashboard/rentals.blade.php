@extends('layouts.dashboard')
@section('title', 'Penyewaan Aktif')
@section('page_label', 'Penyewaan aktif')

@section('content')
<x-dash.page-header title="Penyewaan aktif" description="Perangkat yang sedang Anda sewa atau siap diambil. Sisa waktu dihitung dari jadwal sewa." />

<h2 class="h5 mb-3">Sedang disewa</h2>
<div class="dash-grid dash-grid--even mb-5">
    @forelse ($active as $o)
        @include('dashboard._rental-card', ['o' => $o])
    @empty
        <x-empty-state icon="bi-play-circle" title="Tidak ada penyewaan berjalan">Penyewaan yang sedang berlangsung tampil di sini.</x-empty-state>
    @endforelse
</div>

<h2 class="h5 mb-3">Akan datang</h2>
<div class="dash-grid dash-grid--even">
    @forelse ($upcoming as $o)
        @include('dashboard._rental-card', ['o' => $o])
    @empty
        <x-empty-state icon="bi-calendar-event" title="Belum ada pesanan mendatang">Pesanan yang sudah lunas dan menunggu diambil tampil di sini.</x-empty-state>
    @endforelse
</div>
@endsection
