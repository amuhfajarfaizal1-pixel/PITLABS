{{-- Grafik batang SVG tanpa pustaka. $data = ['Label' => nilai]. Selalu ada tabel pengganti untuk pembaca layar. --}}
@props(['data', 'title', 'format' => 'rupiah'])
@php
    $max = max(1, max($data));
    $step = 10 ** max(0, strlen((string) $max) - 1);
    $top = (int) (ceil($max / $step) * $step);
    $w = 640; $h = 240; $padL = 56; $padB = 28; $padT = 12;
    $plotW = $w - $padL - 8; $plotH = $h - $padB - $padT;
    $n = count($data); $slot = $plotW / max(1, $n); $bw = min(44, $slot * .56);
    $fmt = fn ($v) => $format === 'rupiah' ? ($v >= 1000000 ? rtrim(rtrim(number_format($v / 1000000, 1, ',', '.'), '0'), ',').' jt' : (int) ($v / 1000).' rb') : (string) $v;
@endphp
<figure class="dash-chart">
    <svg viewBox="0 0 {{ $w }} {{ $h }}" role="img" aria-label="{{ $title }}" preserveAspectRatio="xMidYMid meet">
        <title>{{ $title }}</title>
        @for ($i = 0; $i <= 4; $i++)
            @php $y = $padT + $plotH - ($plotH * $i / 4); @endphp
            <line x1="{{ $padL }}" x2="{{ $w - 8 }}" y1="{{ $y }}" y2="{{ $y }}" class="dash-chart__grid"/>
            <text x="{{ $padL - 8 }}" y="{{ $y + 4 }}" text-anchor="end" class="dash-chart__axis">{{ $fmt($top * $i / 4) }}</text>
        @endfor
        @foreach ($data as $label => $value)
            @php
                $bh = $plotH * $value / $top;
                $x = $padL + $slot * $loop->index + ($slot - $bw) / 2;
                $y = $padT + $plotH - $bh;
                $isLast = $loop->last;
            @endphp
            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $bw }}" height="{{ $bh }}" rx="6" class="dash-chart__bar @if($isLast) is-current @endif"/>
            <text x="{{ $x + $bw / 2 }}" y="{{ $h - 8 }}" text-anchor="middle" class="dash-chart__axis">{{ $label }}</text>
        @endforeach
    </svg>
    <table class="visually-hidden">
        <caption>{{ $title }}</caption>
        <thead><tr><th scope="col">Periode</th><th scope="col">Nilai</th></tr></thead>
        <tbody>@foreach ($data as $label => $value)<tr><th scope="row">{{ $label }}</th><td>{{ $format === 'rupiah' ? \App\Support\Pit::rupiah($value) : $value }}</td></tr>@endforeach</tbody>
    </table>
</figure>
