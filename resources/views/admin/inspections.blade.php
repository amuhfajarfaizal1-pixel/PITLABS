@extends('layouts.dashboard')
@section('title','Pemeriksaan Kondisi')
@section('page_label','Pemeriksaan kondisi')
@section('content')
<x-dash.page-header title="Pemeriksaan kondisi" description="Catat kondisi dan kelengkapan perangkat setelah pengembalian diterima. Status unit diperbarui sesuai hasil pemeriksaan. Nominal denda tidak dihitung otomatis." />
@if(count($cases))
    <div class="d-grid gap-4">
    @foreach($cases as $case)
        <x-dash.panel :title="$case['invoice']" :subtitle="$case['customer'].' · '.$case['product'].' · '.$case['unit']">
            <div class="dash-statusgrid mb-3"><div><small>Pesanan</small><x-status-badge group="order" :status="$case['order']" /></div><div><small>Penyewaan</small><x-status-badge group="rental" :status="$case['rental']" /></div></div>
            @if($case['inspection'])<x-alert type="info" class="mb-3" title="Pemeriksaan sebelumnya tersimpan">Pemeriksaan terakhir: {{ $case['inspection']->inspected_at?->timezone('Asia/Makassar')->format('d/m/Y H:i') }}. Simpan kembali hanya bila perlu memperbaiki catatan.</x-alert>@endif
            <form method="POST" action="{{ route('admin.inspections.store',$case['id']) }}" enctype="multipart/form-data">@csrf
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="condition-{{ $case['id'] }}">Kondisi perangkat</label><select class="form-select" id="condition-{{ $case['id'] }}" name="condition_status" required><option value="GOOD">Baik</option><option value="MINOR_ISSUE">Masalah ringan</option><option value="MAJOR_ISSUE">Masalah berat</option><option value="DAMAGED">Rusak</option><option value="LOST">Hilang</option></select></div>
                    <div class="col-md-6"><label class="form-label" for="evidence-{{ $case['id'] }}">Foto bukti (opsional)</label><input class="form-control" id="evidence-{{ $case['id'] }}" type="file" name="evidence" accept="image/jpeg,image/png,image/webp"><div class="form-text">JPG/PNG/WEBP maksimal 5 MB. Disimpan privat.</div></div>
                    <div class="col-12"><label class="form-label" for="notes-{{ $case['id'] }}">Catatan pemeriksaan</label><textarea class="form-control" id="notes-{{ $case['id'] }}" name="inspection_notes" rows="3" minlength="5" maxlength="2000" required>{{ $case['inspection']?->inspection_notes }}</textarea></div>
                    <div class="col-12"><label class="form-label" for="complete-{{ $case['id'] }}">Kelengkapan yang diterima</label><textarea class="form-control" id="complete-{{ $case['id'] }}" name="completeness_notes" rows="2" maxlength="1500">{{ $case['inspection']?->completeness_notes }}</textarea></div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3"><button type="submit" class="btn btn-primary">Simpan pemeriksaan</button>@if($case['inspection']?->evidence_path)<a class="btn btn-outline-primary" href="{{ route('admin.inspections.evidence',$case['inspection']) }}" target="_blank" rel="noopener">Lihat bukti tersimpan</a>@endif</div>
            </form>
        </x-dash.panel>
    @endforeach
    </div>
@else
    <x-empty-state icon="bi-clipboard2-check" title="Tidak ada pengembalian menunggu pemeriksaan">Pesanan akan muncul setelah admin menerima perangkat kembali.</x-empty-state>
@endif
@endsection
