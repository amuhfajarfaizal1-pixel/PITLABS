{{-- Kartu penyewaan. Empat status selalu terpisah. $o = pesanan contoh. --}}
<x-dash.panel :title="$o['product']" :subtitle="'Unit '.$o['unit'].' · '.$o['period']">
    <x-slot:actions><a href="{{ route('invoice.show', $o['invoice']) }}" class="btn btn-sm btn-soft">Lihat invoice</a></x-slot:actions>

    <div class="dash-statusgrid mb-3">
        <div><small>Pesanan</small><x-status-badge group="order" :status="$o['order']" /></div>
        <div><small>Pembayaran</small><x-status-badge group="payment" :status="$o['payment']" /></div>
        <div><small>Pengambilan</small><x-status-badge group="pickup" :status="$o['pickup']" /></div>
        <div><small>Penyewaan</small><x-status-badge group="rental" :status="$o['rental']" /></div>
    </div>

    @if (in_array($o['rental'], ['RENTING', 'DUE_SOON', 'OVERDUE'], true))
        <div class="d-flex justify-content-between small mb-1">
            <span class="fw-semibold">Masa sewa</span>
            <span class="{{ $o['rental'] === 'OVERDUE' ? 'text-danger fw-semibold' : 'text-muted-pit' }}">{{ $o['due_label'] }}</span>
        </div>
        <div class="dash-progress" role="progressbar" aria-label="Progres masa sewa {{ $o['product'] }}" aria-valuenow="{{ $o['progress'] }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ $o['progress'] }}%"></span></div>
    @elseif ($o['payment'] === 'PAID' && $o['pickup'] === 'NOT_PICKED_UP')
        <x-alert type="info">Siap diambil di lokasi admin. Tunjukkan QR pada invoice. Pembayaran berhasil tidak berarti barang sudah diambil.</x-alert>
    @endif
</x-dash.panel>
