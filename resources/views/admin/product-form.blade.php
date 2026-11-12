@extends('layouts.dashboard')
@php $isEdit = $product->exists; @endphp
@section('title', $isEdit ? 'Ubah Produk' : 'Tambah Produk')
@section('page_label', $isEdit ? 'Ubah produk' : 'Tambah produk')

@section('content')
<x-dash.page-header :title="$isEdit ? 'Ubah produk' : 'Tambah produk'" description="Harga, spesifikasi, dan URL Shopee dapat dikosongkan sampai datanya tersedia; halaman produk menampilkan 'Informasi belum tersedia'.">
    <x-slot:actions><a href="{{ route('admin.products') }}" class="btn btn-soft">Kembali</a></x-slot:actions>
</x-dash.page-header>

<form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" data-pit-validate novalidate>
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="dash-grid dash-grid--2">
        <x-dash.panel title="Informasi produk">
            <x-field name="name" label="Nama produk" required maxlength="180" :value="$product->name" />

            <div class="mb-3">
                <label for="product_type" class="form-label">Jenis <span class="pit-required" aria-hidden="true">*</span></label>
                <select id="product_type" name="product_type" class="form-select @error('product_type') is-invalid @enderror" required>
                    <option value="PHYSICAL" @selected(old('product_type', $product->product_type) === 'PHYSICAL')>Perangkat fisik</option>
                    <option value="DIGITAL_SERVICE" @selected(old('product_type', $product->product_type) === 'DIGITAL_SERVICE')>Layanan digital</option>
                </select>
                <div id="product_type-error" class="invalid-feedback">@error('product_type'){{ $message }}@enderror</div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" rows="4" maxlength="5000" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                <div id="description-error" class="invalid-feedback">@error('description'){{ $message }}@enderror</div>
            </div>

            <div class="mb-3">
                <label for="specifications_text" class="form-label">Spesifikasi</label>
                <textarea id="specifications_text" name="specifications_text" rows="5" maxlength="3000" class="form-control pit-mono @error('specifications_text') is-invalid @enderror" aria-describedby="specs-hint">{{ old('specifications_text', $specsText) }}</textarea>
                <div id="specs-hint" class="form-text">Satu baris satu spesifikasi, format <span class="pit-mono">Label: nilai</span>. Maksimal 30 baris.</div>
                <div id="specifications_text-error" class="invalid-feedback">@error('specifications_text'){{ $message }}@enderror</div>
            </div>

            <div class="mb-0">
                <label for="usage_guide" class="form-label">Panduan penggunaan</label>
                <textarea id="usage_guide" name="usage_guide" rows="3" maxlength="5000" class="form-control">{{ old('usage_guide', $product->usage_guide) }}</textarea>
            </div>
        </x-dash.panel>

        <div class="d-grid gap-3 align-content-start">
            <x-dash.panel title="Penawaran">
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" id="rental_enabled" name="rental_enabled" value="1" @checked(old('rental_enabled', $isEdit ? $product->rental_enabled : true))>
                    <label class="form-check-label" for="rental_enabled">Disewakan di PITLABS</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="sale_enabled" name="sale_enabled" value="1" @checked(old('sale_enabled', $product->sale_enabled))>
                    <label class="form-check-label" for="sale_enabled">Dijual via Shopee</label>
                </div>
                @error('rental_enabled')<div class="text-danger small mb-3" role="alert">{{ $message }}</div>@enderror

                <x-field name="rental_daily_rate" label="Harga sewa per hari (Rp)" type="number" min="0" step="1000" inputmode="numeric" :value="$product->rental_daily_rate !== null ? (int) $product->rental_daily_rate : null" hint="Kosongkan jika belum ditetapkan mitra." />
                <x-field name="sale_price" label="Harga jual (Rp)" type="number" min="0" step="1000" inputmode="numeric" :value="$product->sale_price !== null ? (int) $product->sale_price : null" hint="Kosongkan jika belum ditetapkan." />
                <x-field name="shopee_url" label="URL Shopee" type="url" :value="$product->shopee_url" placeholder="https://shopee.co.id/…" hint="Harus diawali https://. Jika kosong, tombol di halaman produk tidak aktif." />
            </x-dash.panel>

            <x-dash.panel title="Status">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $isEdit ? $product->is_active : true))>
                    <label class="form-check-label" for="is_active">Aktif (tampil di katalog)</label>
                </div>
            </x-dash.panel>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Simpan perubahan' : 'Tambah produk' }}</button>
                <a href="{{ route('admin.products') }}" class="btn btn-soft">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
