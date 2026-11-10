@extends('layouts.dashboard')
@section('title', 'Pemeriksaan Kondisi')
@section('page_label', 'Pemeriksaan kondisi')

@section('content')
<x-dash.page-header title="Pemeriksaan kondisi" description="Catat kondisi dan kelengkapan perangkat yang dikembalikan. Denda hanya ditetapkan oleh admin setelah pemeriksaan, tidak otomatis." />

<div class="dash-grid dash-grid--2">
    <x-dash.panel title="Catatan pemeriksaan" subtitle="Isi setelah perangkat diterima kembali">
        @if (count($cases))
            <form data-pit-validate data-pit-simulate novalidate>
                <div class="mb-3">
                    <label for="insCase" class="form-label">Kasus pemeriksaan <span class="pit-required" aria-hidden="true">*</span></label>
                    <select id="insCase" class="form-select" required>
                        @foreach ($cases as $c)<option value="{{ $c['invoice'] }}">{{ $c['invoice'] }} · {{ $c['product'] }} ({{ $c['unit'] }}) · {{ $c['customer'] }}</option>@endforeach
                    </select>
                    <div id="insCase-error" class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="insCondition" class="form-label">Kondisi saat dikembalikan <span class="pit-required" aria-hidden="true">*</span></label>
                    <textarea id="insCondition" class="form-control" rows="3" required minlength="10" maxlength="500" data-msg-min="Jelaskan kondisi minimal 10 karakter."></textarea>
                    <div id="insCondition-error" class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="insComplete" class="form-label">Kelengkapan <span class="pit-required" aria-hidden="true">*</span></label>
                    <textarea id="insComplete" class="form-control" rows="2" required maxlength="300"></textarea>
                    <div class="form-text">Sebutkan aksesori dan kelengkapan yang diterima kembali.</div>
                    <div id="insComplete-error" class="invalid-feedback"></div>
                </div>

                <fieldset class="mb-3">
                    <legend class="form-label">Hasil pemeriksaan</legend>
                    <div class="form-check"><input class="form-check-input" type="radio" name="insResult" id="rOk" value="ok" checked><label class="form-check-label" for="rOk">Tidak ada masalah, sewa selesai</label></div>
                    <div class="form-check"><input class="form-check-input" type="radio" name="insResult" id="rDamage" value="damage"><label class="form-check-label" for="rDamage">Ada kerusakan</label></div>
                    <div class="form-check"><input class="form-check-input" type="radio" name="insResult" id="rLost" value="lost"><label class="form-check-label" for="rLost">Ada kehilangan</label></div>
                </fieldset>

                <div data-show-when="insResult:damage,lost" hidden class="dash-panel p-3 mb-3 bg-light">
                    <div class="mb-3">
                        <label for="insCategory" class="form-label">Kategori</label>
                        <select id="insCategory" class="form-select"><option>Ringan</option><option>Sedang</option><option>Berat</option></select>
                    </div>
                    <div class="mb-3">
                        <label for="insReason" class="form-label">Alasan dan bukti <span class="pit-required" aria-hidden="true">*</span></label>
                        <textarea id="insReason" class="form-control" rows="2" maxlength="400" data-required-when-visible></textarea>
                        <div id="insReason-error" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="insPhoto" class="form-label">Foto bukti</label>
                        <input id="insPhoto" type="file" class="form-control" accept="image/*">
                    </div>
                    <p class="mb-0 small">Nilai denda: <x-pending field="inspection.fine_amount" />. Nominal mengikuti ketentuan mitra dan dicatat terpisah dari biaya sewa.</p>
                </div>

                <button type="submit" class="btn btn-primary">Simpan hasil pemeriksaan</button>
                <div data-pit-form-status hidden tabindex="-1" class="mt-3"><x-alert type="success" title="Tersimpan (simulasi)">Hasil pemeriksaan belum disimpan ke database.</x-alert></div>
            </form>
        @else
            <x-empty-state icon="bi-clipboard2-check" title="Tidak ada pemeriksaan tertunda">Perangkat yang sudah diterima kembali akan muncul di sini.</x-empty-state>
        @endif
    </x-dash.panel>

    <x-dash.panel title="Menunggu pemeriksaan" flush>
        <ul class="dash-list">
            @forelse ($cases as $c)
                <li class="dash-list__item">
                    <div class="dash-list__main"><strong>{{ $c['product'] }} <span class="pit-mono">{{ $c['unit'] }}</span></strong><small>{{ $c['customer'] }} · {{ $c['invoice'] }}</small></div>
                    <x-status-badge group="rental" :status="$c['rental']" />
                </li>
            @empty
                <li class="dash-list__item text-muted-pit">Tidak ada.</li>
            @endforelse
        </ul>
    </x-dash.panel>
</div>
@endsection
