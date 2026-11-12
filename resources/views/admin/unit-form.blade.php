@extends('layouts.dashboard')
@php $isEdit = $unit->exists; @endphp
@section('title', $isEdit ? 'Ubah Unit' : 'Tambah Unit')
@section('page_label', $isEdit ? 'Ubah unit' : 'Tambah unit')

@section('content')
<x-dash.page-header :title="$isEdit ? 'Ubah unit '.$unit->unit_code : 'Tambah unit'" description="Unit hanya untuk produk perangkat fisik. Kode unit harus unik dan dipakai pada invoice dan verifikasi pengambilan.">
    <x-slot:actions><a href="{{ route('admin.inventory') }}" class="btn btn-soft">Kembali</a></x-slot:actions>
</x-dash.page-header>

<div class="row"><div class="col-xl-7">
<form method="POST" action="{{ $isEdit ? route('admin.units.update', $unit) : route('admin.units.store') }}" data-pit-validate novalidate>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <x-dash.panel>
        <div class="mb-3">
            <label for="product_id" class="form-label">Produk <span class="pit-required" aria-hidden="true">*</span></label>
            <select id="product_id" name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                <option value="">Pilih produk fisik</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" @selected((int) old('product_id', $unit->product_id) === $p->id)>{{ $p->name }}@unless($p->is_active) (nonaktif)@endunless</option>
                @endforeach
            </select>
            <div id="product_id-error" class="invalid-feedback">@error('product_id'){{ $message }}@enderror</div>
        </div>

        <x-field name="unit_code" label="Kode unit" required maxlength="80" :value="$unit->unit_code" pattern="[A-Z0-9][A-Z0-9\-]{2,78}"
                 data-pattern-msg="Hanya huruf besar, angka, dan tanda minus. Contoh: JON-004" hint="Contoh: JON-004. Tidak dapat sama dengan unit lain." />
        <x-field name="serial_number" label="Nomor seri" maxlength="120" :value="$unit->serial_number" />

        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <label for="condition_status" class="form-label">Kondisi</label>
                <select id="condition_status" name="condition_status" class="form-select">
                    @foreach (config('pitlabs.status.condition') as $code => $def)
                        <option value="{{ $code }}" @selected(old('condition_status', $unit->condition_status) === $code)>{{ $def['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label for="operational_status" class="form-label">Status operasional</label>
                <select id="operational_status" name="operational_status" class="form-select">
                    @foreach (config('pitlabs.status.operational') as $code => $def)
                        <option value="{{ $code }}" @selected(old('operational_status', $unit->operational_status) === $code)>{{ $def['label'] }}</option>
                    @endforeach
                </select>
                <div class="form-text">Hanya unit berstatus Aktif yang bisa disewa.</div>
            </div>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Catatan</label>
            <textarea id="notes" name="notes" rows="3" maxlength="1000" class="form-control">{{ old('notes', $unit->notes) }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan perubahan' : 'Tambah unit' }}</button>
            <a href="{{ route('admin.inventory') }}" class="btn btn-soft">Batal</a>
        </div>
    </x-dash.panel>
</form>
</div></div>
@endsection
