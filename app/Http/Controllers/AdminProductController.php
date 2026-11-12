<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CRUD produk (Fase 5B). Produk tidak dihapus: dinonaktifkan agar riwayat pesanan tetap utuh.
 */
class AdminProductController extends Controller
{
    public function index(): View
    {
        $rows = Product::withCount('units')->orderBy('id')->get()->map(function (Product $p) {
            return [
                'id'      => $p->id,
                'stack'   => [$p->name, '/produk/'.$p->slug],
                'type'    => $p->isDigital() ? 'Layanan digital' : 'Perangkat fisik',
                'units'   => $p->isDigital() ? 'Tanpa unit fisik' : $p->units_count.' unit',
                'rental'  => $p->rental_enabled ? 'Aktif' : 'Nonaktif',
                'sale'    => $p->sale_enabled ? 'Aktif' : 'Nonaktif',
                'rate'    => $p->rental_daily_rate !== null ? (int) $p->rental_daily_rate : null,
                'status'  => $p->is_active ? 'ACTIVE' : 'INACTIVE',
                '_actions' => array_values(array_filter([
                    ['label' => 'Ubah', 'variant' => 'soft', 'href' => route('admin.products.edit', $p)],
                    $p->is_active ? ['label' => 'Lihat', 'variant' => 'soft', 'href' => route('catalog.show', $p->slug)] : null,
                    ['label' => $p->is_active ? 'Nonaktifkan' : 'Aktifkan', 'variant' => 'soft', 'form' => [
                        'action'  => route('admin.products.toggle', $p),
                        'confirm' => ($p->is_active ? 'Nonaktifkan' : 'Aktifkan').' produk '.$p->name.'? '.($p->is_active ? 'Produk tidak lagi tampil di katalog.' : ''),
                    ]],
                ])),
            ];
        })->all();

        return view('admin.products', ['rows' => $rows]);
    }

    public function create(): View
    {
        return view('admin.product-form', ['product' => new Product(['product_type' => 'PHYSICAL', 'rental_enabled' => true, 'is_active' => true]), 'specsText' => '']);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($this->payload($request) + ['slug' => Product::uniqueSlug($request->input('name'))]);

        return redirect()->route('admin.products')->with('status', 'Produk "'.$product->name.'" ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $specsText = collect($product->specifications ?? [])->map(fn ($v, $k) => "{$k}: {$v}")->implode("\n");

        return view('admin.product-form', compact('product', 'specsText'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($this->payload($request)); // slug tetap agar URL dan SEO tidak berubah

        return redirect()->route('admin.products')->with('status', 'Produk "'.$product->name.'" diperbarui.');
    }

    public function toggle(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('status', 'Produk "'.$product->name.'" '.($product->is_active ? 'diaktifkan.' : 'dinonaktifkan.'));
    }

    private function payload(ProductRequest $request): array
    {
        $type = $request->input('product_type');
        [$specs] = Product::parseSpecifications($request->input('specifications_text'));

        return [
            'name'              => trim($request->input('name')),
            'product_type'      => $type,
            'category_id'       => Category::where('slug', $type === 'PHYSICAL' ? 'perangkat-fisik' : 'layanan-digital')->value('id'),
            'description'       => $request->input('description') ?: null,
            'usage_guide'       => $request->input('usage_guide') ?: null,
            'specifications'    => $specs ?: null,
            'rental_enabled'    => $request->boolean('rental_enabled'),
            'sale_enabled'      => $request->boolean('sale_enabled'),
            'rental_daily_rate' => $request->filled('rental_daily_rate') ? $request->input('rental_daily_rate') : null,
            'sale_price'        => $request->filled('sale_price') ? $request->input('sale_price') : null,
            'shopee_url'        => $request->input('shopee_url') ?: null,
            'is_active'         => $request->boolean('is_active'),
        ];
    }
}
