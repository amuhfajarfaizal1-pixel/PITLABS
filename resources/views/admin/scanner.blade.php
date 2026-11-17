@extends('layouts.dashboard')
@section('title','QR Scanner')
@section('page_label','QR Scanner')
@section('content')
<x-dash.page-header title="QR Scanner" description="Pindai QR pada invoice atau masukkan nomor invoice. Server memverifikasi tanda tangan QR dan mencocokkan invoice ke database. Pemindaian tidak mengubah status transaksi." />
<div class="dash-grid dash-grid--even">
    <x-dash.panel title="Pindai dengan kamera atau input manual" subtitle="Kamera membutuhkan HTTPS (atau localhost) dan izin kamera browser.">
        <div class="dash-scan mb-3"><video data-qr-video autoplay muted playsinline class="w-100 h-100" style="object-fit:cover" aria-label="Pratinjau kamera pemindai QR"></video><div class="dash-scan__frame" aria-hidden="true"></div></div>
        <div class="d-flex flex-wrap gap-2 mb-3"><button type="button" class="btn btn-primary" data-qr-start><i class="bi bi-camera-video" aria-hidden="true"></i> Aktifkan kamera</button><button type="button" class="btn btn-soft" data-qr-stop hidden>Hentikan kamera</button></div>
        <div data-qr-status class="alert alert-info" role="status" hidden></div>
        <form method="POST" action="{{ route('admin.scanner.lookup') }}" data-qr-scan-form class="mt-3">@csrf
            <label for="invoiceInput" class="form-label">Nomor invoice atau payload QR</label>
            <input id="invoiceInput" name="scanner_input" data-qr-input class="form-control pit-mono" maxlength="500" required autocomplete="off" spellcheck="false" placeholder="INV-… atau pindai QR">
            @error('scanner_input')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <div class="form-text">Input nomor invoice adalah pencarian manual admin. Payload hasil scan akan diverifikasi dengan HMAC dan token hash pada database.</div>
            <button class="btn btn-outline-primary mt-3" type="submit"><i class="bi bi-search" aria-hidden="true"></i> Verifikasi ke database</button>
        </form>
    </x-dash.panel>
    <div>
        @if($scanOrder)
            @php $scanData = $scanOrder->toViewArray(); $eligible = $scanOrder->payment_status === 'PAID' && $scanOrder->pickup_status === 'NOT_PICKED_UP' && $scanOrder->order_status !== 'CANCELLED'; @endphp
            <x-dash.panel :title="$scanData['invoice']" :subtitle="$scanData['customer'].' · '.$scanData['product'].' ('.$scanData['unit'].')'">
                <p class="small text-muted-pit mb-3">Metode verifikasi: {{ $scanMode === 'qr' ? 'QR bertanda tangan, cocok dengan hash database' : 'Pencarian nomor invoice manual' }}.</p>
                <div class="dash-statusgrid mb-3">
                    <div><small>Pesanan</small><x-status-badge group="order" :status="$scanData['order']" /></div>
                    <div><small>Pembayaran</small><x-status-badge group="payment" :status="$scanData['payment']" /></div>
                    <div><small>Pengambilan</small><x-status-badge group="pickup" :status="$scanData['pickup']" /></div>
                    <div><small>Penyewaan</small><x-status-badge group="rental" :status="$scanData['rental']" /></div>
                </div>
                <dl class="dash-kv"><dt>Periode</dt><dd>{{ $scanData['start_full'] }} – {{ $scanData['end_full'] }}</dd><dt>Total</dt><dd>{{ $scanData['total'] === null ? 'Informasi belum tersedia' : \App\Support\Pit::rupiah($scanData['total']) }}</dd><dt>Metode pembayaran</dt><dd>{{ $scanData['method'] }}</dd><dt>Nomor pesanan</dt><dd class="pit-mono">{{ $scanData['order_number'] }}</dd></dl>
                @if($eligible)
                    <x-alert type="success" title="Invoice ditemukan dan memenuhi syarat">Pembayaran lunas dan belum diambil. Pastikan identitas pelanggan serta unit yang diserahkan sudah sesuai.</x-alert>
                    <form method="POST" action="{{ route('admin.pickups.confirm',$scanOrder) }}" onsubmit="return confirm('Konfirmasi bahwa perangkat sudah diserahkan secara fisik kepada pelanggan?')">@csrf<button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-up" aria-hidden="true"></i> Konfirmasi pengambilan</button></form>
                @else
                    <x-alert type="warning" title="Belum dapat diserahkan">@if($scanOrder->order_status === 'CANCELLED')Pesanan dibatalkan.@elseif($scanOrder->payment_status !== 'PAID')Pembayaran belum lunas.@elseif($scanOrder->pickup_status === 'PICKED_UP')Perangkat sudah dicatat diambil.@elseStatus pesanan tidak memenuhi syarat pengambilan.@endif</x-alert>
                @endif
            </x-dash.panel>
        @else
            <x-empty-state icon="bi-qr-code-scan" title="Belum ada invoice diverifikasi">Hasil pencarian atau pemindaian yang cocok dengan database akan ditampilkan di sini. Belum ada status transaksi yang diubah.</x-empty-state>
        @endif
    </div>
</div>
@endsection
@push('scripts')<script src="{{ asset('assets/js/admin-scanner.js') }}" defer></script>@endpush
