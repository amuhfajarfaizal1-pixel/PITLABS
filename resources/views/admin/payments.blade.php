@extends('layouts.dashboard')
@section('title','Verifikasi Pembayaran')
@section('page_label','Verifikasi pembayaran')
@section('content')
<x-dash.page-header title="Verifikasi pembayaran" description="Pembayaran COD dikonfirmasi manual setelah uang diterima secara fisik. QRIS/Virtual Account belum diaktifkan karena payment gateway belum terhubung." />
<x-alert type="info" class="mb-4" title="Catat uang yang benar-benar diterima">Jika total pesanan belum tersedia karena tarif mitra belum dimasukkan, admin mengisi nominal yang benar-benar diterima sesuai kesepakatan mitra. Sistem tidak membuat tarif atau nilai pembayaran sendiri. Status pengambilan tetap terpisah.</x-alert>
@if($payments->isEmpty())
    <x-empty-state icon="bi-cash-coin" title="Tidak ada pembayaran menunggu verifikasi">Pesanan COD yang belum dibayar akan muncul di sini setelah pelanggan melakukan checkout.</x-empty-state>
@else
    <div class="d-grid gap-3">
    @foreach($payments as $payment)
        @php $order = $payment->order; $item = $order?->items?->first(); @endphp
        @if($order)
            <x-dash.panel :title="$order->invoice?->invoice_number ?? $order->order_number" :subtitle="($order->user?->name ?? 'Pelanggan').' · '.($item?->product_name_snapshot ?? 'Produk'). ' · '.($item?->unit_code_snapshot ?? 'Unit belum ditentukan')">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3"><small class="text-muted-pit d-block">Metode</small><strong>{{ $payment->payment_method }}</strong></div>
                    <div class="col-md-3"><small class="text-muted-pit d-block">Total pada pesanan</small><strong>{{ $order->total_amount === null ? 'Belum ditetapkan' : \App\Support\Pit::rupiah((int) $order->total_amount) }}</strong></div>
                    <div class="col-md-6">
                        @if($payment->payment_method === 'COD' && $order->order_status !== 'CANCELLED')
                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="row g-2 align-items-end" onsubmit="return confirm('Pastikan uang COD telah diterima dan nominal sesuai kesepakatan. Lanjutkan?')">@csrf
                                <div class="col"><label class="form-label" for="amount-{{ $payment->id }}">Nominal uang diterima (Rp)</label><input class="form-control" id="amount-{{ $payment->id }}" name="amount_received" type="number" min="1" step="0.01" value="{{ $payment->amount ?? $order->total_amount ?? '' }}" required></div>
                                <div class="col-auto"><button class="btn btn-primary" type="submit">Konfirmasi diterima</button></div>
                            </form>
                        @else
                            <span class="text-muted-pit">Metode online belum dapat diverifikasi otomatis. Hubungkan payment gateway sebelum mengaktifkannya.</span>
                        @endif
                    </div>
                </div>
            </x-dash.panel>
        @endif
    @endforeach
    </div>
@endif
@endsection
