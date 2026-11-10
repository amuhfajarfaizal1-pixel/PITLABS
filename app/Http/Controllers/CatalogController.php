<?php

namespace App\Http\Controllers;

use App\Support\MockAvailability;
use App\Support\MockCatalog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $q    = mb_substr(trim((string) $request->query('q', '')), 0, 80);
        $jenis = in_array($request->query('jenis'), ['physical', 'digital'], true) ? $request->query('jenis') : null;
        $tersedia = $request->boolean('tersedia');

        return view('catalog.index', [
            'products' => MockCatalog::search($q, $jenis, $tersedia),
            'total'    => count(MockCatalog::all()),
            'q'        => $q,
            'jenis'    => $jenis,
            'tersedia' => $tersedia,
        ]);
    }

    public function show(string $slug): View
    {
        $product = MockCatalog::find($slug) ?? abort(404);

        $related = array_slice(array_values(array_filter(
            MockCatalog::all(),
            fn ($p) => $p['slug'] !== $slug
        )), 0, 3);

        return view('catalog.show', [
            'product' => $product,
            'units'   => MockAvailability::unitsFor($slug),
            'related' => $related,
        ]);
    }
}
