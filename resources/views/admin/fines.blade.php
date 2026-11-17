@extends('layouts.dashboard')
@section('title','Pengelolaan Denda')
@section('page_label','Pengelolaan denda')
@section('content')
<x-dash.page-header title="Pengelolaan denda" description="Denda dicatat terpisah dari biaya sewa. Admin memasukkan nominal hanya setelah memeriksa bukti dan menerapkan kebijakan yang disahkan mitra." />
<x-alert type="warning" class="mb-4" title="Tidak ada tarif denda otomatis">Tarif keterlambatan, kerusakan, dan kehilangan belum tercantum sebagai kebijakan terverifikasi. Sistem tidak menghitung atau mengarang nominal. Catatan baru dapat disimpan tanpa nominal, kemudian dinilai manual oleh admin.</x-alert>
<x-dash.panel title="Catat denda / tindak lanjut" class="mb-4">
    <form method="POST" action="{{ route('admin.fines.store') }}">@csrf
        <div class="row g-3"><div class="col-md-5"><label class="form-label" for="fineOrder">Pesanan</label><select id="fineOrder" name="order_id" class="form-select" required><option value="">Pilih invoice</option>@foreach($orders as $order)<option value="{{ $order->id }}">{{ $order->invoice?->invoice_number ?? $order->order_number }} · {{ $order->user?->name }} · {{ $order->items->first()?->product_name_snapshot }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="fineType">Jenis</label><select id="fineType" name="fine_type" class="form-select" required><option value="LATE">Keterlambatan</option><option value="DAMAGE">Kerusakan</option><option value="LOSS">Kehilangan</option><option value="OTHER">Lainnya</option></select></div>
        <div class="col-md-4"><label class="form-label" for="fineDesc">Dasar / catatan</label><input id="fineDesc" name="description" class="form-control" minlength="5" maxlength="2000" required placeholder="Jelaskan dasar keputusan"></div></div>
        <button class="btn btn-primary mt-3" type="submit">Catat tanpa nominal</button>
    </form>
</x-dash.panel>
<div class="d-grid gap-3">
@forelse($fines as $fine)
    <x-dash.panel :title="($fine->order?->invoice?->invoice_number ?? $fine->order?->order_number ?? 'Pesanan').' · '.(['LATE'=>'Keterlambatan','DAMAGE'=>'Kerusakan','LOSS'=>'Kehilangan','OTHER'=>'Lainnya'][$fine->fine_type] ?? $fine->fine_type)" :subtitle="($fine->order?->user?->name ?? '-').' · Dicatat '.($fine->created_at?->timezone('Asia/Makassar')->format('d/m/Y H:i') ?? '-')">
        <p>{{ $fine->description }}</p><div class="d-flex flex-wrap align-items-center gap-3 mb-3"><x-status-badge group="fine" :status="$fine->fine_status" /><strong>{{ $fine->amount === null ? 'Nominal belum ditetapkan' : \App\Support\Pit::rupiah((int)$fine->amount) }}</strong></div>
        @if($fine->fine_status === 'PENDING_REVIEW')
            <form method="POST" action="{{ route('admin.fines.assess',$fine) }}" class="row g-2 align-items-end">@csrf<div class="col-md-3"><label class="form-label" for="amount-{{ $fine->id }}">Nominal sesuai keputusan (Rp)</label><input id="amount-{{ $fine->id }}" type="number" min="1" step="1" name="amount" class="form-control" required></div><div class="col-md-5"><label class="form-label" for="note-{{ $fine->id }}">Catatan admin</label><input id="note-{{ $fine->id }}" name="admin_note" class="form-control" maxlength="1500"></div><div class="col-md-4"><button class="btn btn-primary" type="submit">Tetapkan nominal</button></div></form>
        @elseif($fine->fine_status === 'ASSESSED')
            <div class="d-flex flex-wrap gap-3"><form method="POST" action="{{ route('admin.fines.paid',$fine) }}" class="d-flex flex-wrap gap-2 align-items-end" onsubmit="return confirm('Pastikan pembayaran denda sebesar nominal yang ditetapkan benar-benar sudah diterima?')">@csrf<label class="small" for="fine-paid-{{ $fine->id }}">Nominal diterima (Rp)<input id="fine-paid-{{ $fine->id }}" class="form-control" type="number" name="amount_received" min="1" step="0.01" value="{{ $fine->amount }}" required></label><button class="btn btn-primary" type="submit">Konfirmasi pembayaran</button></form><form method="POST" action="{{ route('admin.fines.waive',$fine) }}" class="d-flex flex-wrap gap-2 align-items-end">@csrf<label class="small">Alasan pembebasan<input class="form-control" name="admin_note" required minlength="5" maxlength="1500"></label><button class="btn btn-outline-danger" type="submit">Bebaskan denda</button></form></div>
        @elseif($fine->admin_note)<p class="small text-muted-pit mb-0">Catatan admin: {{ $fine->admin_note }}</p>@endif
    </x-dash.panel>
@empty
    <x-empty-state icon="bi-check2-circle" title="Belum ada catatan denda">Tidak ada denda yang tercatat di database.</x-empty-state>
@endforelse
</div>
@endsection
