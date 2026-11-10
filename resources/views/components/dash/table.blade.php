{{--
    Tabel data dashboard (cari, filter status, aksi simulasi).
    columns: [['key','label','type'(text|mono|stack|status|money|pending|actions),'group'?,'class'?]]
    rows   : array; baris boleh punya '_sim' => [kolom => status sesudah] dan '_actions' => [[label,variant,href?,sim?[title,text,message]]]
    Slot 'filters' (opsional): <select data-pit-table-filter data-key="payment"> ...
--}}
@props(['id', 'columns', 'rows', 'searchable' => true, 'placeholder' => 'Cari…', 'caption' => '', 'empty' => 'Tidak ada data untuk ditampilkan.'])

<div class="dash-table" data-pit-table="#{{ $id }}">
    @if ($searchable || isset($filters))
        <div class="dash-table__toolbar">
            @if ($searchable)
                <div class="dash-search">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <label for="{{ $id }}-search" class="visually-hidden">Cari di tabel</label>
                    <input type="search" id="{{ $id }}-search" class="form-control" placeholder="{{ $placeholder }}" data-pit-table-search="#{{ $id }}">
                </div>
            @endif
            @isset($filters){{ $filters }}@endisset
            <span class="dash-table__count" role="status" data-pit-table-count></span>
        </div>
    @endif

    <div class="table-responsive">
        <table id="{{ $id }}" class="table dash-table__table align-middle">
            <caption class="visually-hidden">{{ $caption ?: 'Tabel data' }}</caption>
            <thead>
                <tr>
                    @foreach ($columns as $col)
                        <th scope="col" class="{{ $col['class'] ?? '' }} {{ ($col['type'] ?? '') === 'money' ? 'text-end' : '' }}">{{ $col['label'] ?? '' }}@if (($col['type'] ?? '') === 'actions')<span class="visually-hidden">Aksi</span>@endif</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    @php
                        $haystack = mb_strtolower(implode(' ', array_map(fn ($v) => is_array($v) ? implode(' ', array_filter($v, 'is_scalar')) : (is_scalar($v) ? (string) $v : ''), array_filter($row, fn ($k) => ! str_starts_with((string) $k, '_'), ARRAY_FILTER_USE_KEY))));
                    @endphp
                    <tr id="row-{{ $row['id'] }}" data-search="{{ $haystack }}"
                        @foreach ($columns as $col) @if (($col['type'] ?? '') === 'status') data-{{ $col['key'] }}="{{ $row[$col['key']] }}" @endif @endforeach>
                        @foreach ($columns as $col)
                            @php $type = $col['type'] ?? 'text'; $key = $col['key'] ?? null; $val = $key ? ($row[$key] ?? null) : null; @endphp
                            <td class="{{ $col['class'] ?? '' }} {{ $type === 'money' ? 'text-end' : '' }} {{ $type === 'actions' ? 'text-end text-nowrap' : '' }}">
                                @switch($type)
                                    @case('mono')<span class="pit-mono">{{ $val }}</span>@break
                                    @case('stack')<span class="dash-stack"><strong>{{ $val[0] }}</strong><small>{{ $val[1] ?? '' }}</small></span>@break
                                    @case('money')@if ($val === null)<x-pending :field="$col['pending'] ?? 'money'" />@else<span class="dash-money">{{ \App\Support\Pit::rupiah($val) }}</span>@endif @break
                                    @case('pending')<x-pending :field="$col['pending'] ?? $key" />@break
                                    @case('status')
                                        @if (isset($row['_sim'][$key]))
                                            <span data-sim-before><x-status-badge :group="$col['group']" :status="$val" /></span>
                                            <span data-sim-after hidden><x-status-badge :group="$col['group']" :status="$row['_sim'][$key]" /></span>
                                        @else
                                            <x-status-badge :group="$col['group']" :status="$val" />
                                        @endif
                                    @break
                                    @case('actions')
                                        @foreach ($row['_actions'] ?? [] as $a)
                                            @if (isset($a['sim']))
                                                <button type="button" class="btn btn-sm btn-{{ $a['variant'] ?? 'soft' }}" data-sim-action
                                                        data-pit-sim data-sim-row="#row-{{ $row['id'] }}"
                                                        data-sim-title="{{ $a['sim'][0] }}" data-sim-text="{{ $a['sim'][1] }}" data-sim-message="{{ $a['sim'][2] }}">{{ $a['label'] }}</button>
                                            @elseif (isset($a['href']))
                                                <a href="{{ $a['href'] }}" class="btn btn-sm btn-{{ $a['variant'] ?? 'soft' }}">{{ $a['label'] }}</a>
                                            @endif
                                        @endforeach
                                    @break
                                    @default{{ $val }}
                                @endswitch
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) }}">{{ $empty }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="dash-table__empty" data-pit-table-empty hidden>
        <x-empty-state icon="bi-search" title="Tidak ada hasil">Tidak ada baris yang cocok dengan pencarian atau filter. Ubah kata kunci atau pilih "Semua".</x-empty-state>
    </div>
</div>
