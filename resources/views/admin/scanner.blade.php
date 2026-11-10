@extends('layouts.dashboard')
@section('title', 'QR Scanner')
@section('page_label', 'QR Scanner')

@section('content')
<x-dash.page-header title="QR Scanner" description="Pindai QR invoice atau cari nomor invoice untuk verifikasi manual. Admin memeriksa identitas dan status pesanan sebelum mencatat pengambilan." />

<div class="dash-grid dash-grid--even">
    <x-dash.panel title="Pindai atau cari invoice">
        <div class="dash-scan mb-3" role="img" aria-label="Area pemindaian kamera, belum aktif">
            <div class="dash-scan__frame"></div>
            <p class="dash-scan__text mb-0">Pemindaian kamera aktif setelah backend tersambung (Fase 5). Gunakan pencarian invoice di bawah.</p>
        </div>

        <form class="d-flex gap-2 flex-wrap" data-pit-invoice-lookup="#scanResults" data-not-found="#scanNotFound" role="search" aria-label="Cari nomor invoice">
            <div class="flex-fill">
                <label for="invoiceInput" class="form-label">Nomor invoice</label>
                <input id="invoiceInput" type="text" class="form-control pit-mono" placeholder="INV-CONTOH-004" autocomplete="off" spellcheck="false">
                <div class="form-text">Coba INV-CONTOH-004 (siap diambil), INV-CONTOH-005 (belum dibayar), atau INV-CONTOH-001 (sudah diambil).</div>
            </div>
            <div class="align-self-start" style="padding-top: 2rem"><button type="submit" class="btn btn-primary">Cari</button></div>
        </form>
    </x-dash.panel>

    <div id="scanResults" aria-live="polite">
        <x-empty-state icon="bi-qr-code-scan" title="Belum ada invoice dipindai">Hasil pengecekan invoice muncul di sini.</x-empty-state>

        <div id="scanNotFound" hidden class="mt-3"><x-alert type="danger" title="Invoice tidak ditemukan">Periksa kembali nomor invoice, lalu coba lagi.</x-alert></div>

        @foreach ($orders as $o)
            @php
                $eligible = $o['payment'] === 'PAID' && $o['order'] !== 'CANCELLED' && $o['pickup'] === 'NOT_PICKED_UP';
                $reason = match (true) {
                    $o['order'] === 'CANCELLED'      => 'Pesanan dibatalkan.',
                    $o['payment'] !== 'PAID'         => 'Pembayaran belum lunas.',
                    $o['pickup'] === 'PICKED_UP'     => 'Perangkat sudah diambil sebelumnya.',
                    default                          => null,
                };
            @endphp
            <div class="mt-3" id="scan-{{ $o['invoice'] }}" data-invoice="{{ $o['invoice'] }}" hidden>
                <x-dash.panel :title="$o['invoice']" :subtitle="$o['customer'].' · '.$o['product'].' ('.$o['unit'].')'">
                    <div class="dash-statusgrid mb-3">
                        <div><small>Pesanan</small><x-status-badge group="order" :status="$o['order']" /></div>
                        <div><small>Pembayaran</small><x-status-badge group="payment" :status="$o['payment']" /></div>
                        <div><small>Pengambilan</small>
                            @if ($eligible)
                                <span data-sim-before><x-status-badge group="pickup" :status="$o['pickup']" /></span>
                                <span data-sim-after hidden><x-status-badge group="pickup" status="PICKED_UP" /></span>
                            @else
                                <x-status-badge group="pickup" :status="$o['pickup']" />
                            @endif
                        </div>
                        <div><small>Penyewaan</small><x-status-badge group="rental" :status="$o['rental']" /></div>
                    </div>

                    @if ($eligible)
                        <div data-sim-before>
                            <x-alert type="success" class="mb-3" title="Invoice valid">Lunas dan belum diambil. Periksa identitas pelanggan, lalu catat pengambilan.</x-alert>
                            <button type="button" class="btn btn-primary" data-sim-action data-pit-sim data-sim-row="#scan-{{ $o['invoice'] }}"
                                    data-sim-title="Konfirmasi pengambilan" data-sim-text="Identitas {{ $o['customer'] }} sudah diperiksa dan perangkat {{ $o['unit'] }} diserahkan."
                                    data-sim-message="Pengambilan {{ $o['invoice'] }} tercatat">Konfirmasi pengambilan</button>
                        </div>
                        <div data-sim-after hidden><x-alert type="success" title="Pengambilan tercatat">Status pengambilan berubah. Status pembayaran tetap.</x-alert></div>
                    @else
                        <x-alert type="warning" title="Belum dapat diserahkan">{{ $reason }}</x-alert>
                    @endif
                </x-dash.panel>
            </div>
        @endforeach
    </div>
</div>
@endsection
