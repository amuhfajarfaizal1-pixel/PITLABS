<?php

namespace App\Http\Controllers;

use App\Support\MockAvailability;
use App\Support\MockCatalog;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function show(Request $request): View
    {
        $products = MockCatalog::all();
        $slugs = array_column($products, 'slug');
        $today = MockAvailability::today();

        $result = null;
        $errors = null;

        if ($request->query->has('mulai')) {
            $validator = Validator::make($request->query(), [
                'produk' => ['required', 'in:'.implode(',', $slugs)],
                'mulai'  => ['required', 'date_format:Y-m-d', function ($attr, $value, $fail) use ($today) {
                    $d = CarbonImmutable::createFromFormat('Y-m-d', $value, MockAvailability::TZ);
                    if ($d && $d->startOfDay() < $today) {
                        $fail('Tanggal mulai tidak boleh sebelum hari ini.');
                    }
                }],
                'durasi' => ['required', 'integer', 'min:1', 'max:90'],
                'satuan' => ['required', 'in:hari,minggu,bulan'],
            ], [
                'produk.required'    => 'Pilih produk yang ingin disewa.',
                'produk.in'          => 'Produk tidak ditemukan. Pilih dari daftar.',
                'mulai.required'     => 'Pilih tanggal mulai sewa.',
                'mulai.date_format'  => 'Format tanggal tidak valid.',
                'durasi.required'    => 'Isi durasi sewa.',
                'durasi.integer'     => 'Durasi harus berupa angka bulat.',
                'durasi.min'         => 'Durasi minimal 1.',
                'durasi.max'         => 'Durasi terlalu panjang. Hubungi admin untuk sewa jangka panjang.',
                'satuan.required'    => 'Pilih satuan durasi.',
                'satuan.in'          => 'Satuan durasi tidak valid.',
            ]);

            if ($validator->fails()) {
                $errors = $validator;
            } else {
                $result = $this->compute($request, $today);
            }
        }

        return view('availability', [
            'products' => $products,
            'result'   => $result,
            'today'    => $today->toDateString(),
        ])->withErrors($errors ?? []);
    }

    private function compute(Request $request, CarbonImmutable $today): array
    {
        $product = MockCatalog::find($request->query('produk'));
        $start = CarbonImmutable::createFromFormat('Y-m-d', $request->query('mulai'), MockAvailability::TZ)->startOfDay();
        $n = (int) $request->query('durasi');

        $end = match ($request->query('satuan')) {
            'minggu' => $start->addWeeks($n),
            'bulan'  => $start->addMonthsNoOverflow($n),
            default  => $start->addDays($n),
        };

        $fmt = fn (CarbonImmutable $d) => $d->locale('id')->translatedFormat('j F Y');
        $units = MockAvailability::check($product['slug'], $start, $end);

        if ($units !== null) {
            foreach ($units as &$u) {
                $u['conflict_label'] = $u['conflict']
                    ? $fmt($u['conflict'][0]).' – '.$fmt($u['conflict'][1]->subDay())
                    : null;
            }
            unset($u);
        }

        return [
            'product'    => $product,
            'start'      => $fmt($start),
            'end'        => $fmt($end),
            'days'       => (int) $start->diffInDays($end),
            'units'      => $units,
            'free_count' => $units ? count(array_filter($units, fn ($u) => $u['free'])) : null,
        ];
    }
}
