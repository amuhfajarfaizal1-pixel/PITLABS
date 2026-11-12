<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryUnitRequest;
use App\Models\InventoryUnit;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CRUD unit inventaris (Fase 5B). Unit tidak dihapus: dipensiunkan (RETIRED) agar riwayat tetap utuh.
 */
class AdminUnitController extends Controller
{
    public function index(): View
    {
        $rows = InventoryUnit::with('product')->orderBy('product_id')->orderBy('unit_code')->get()->map(fn (InventoryUnit $u) => [
            'id'          => $u->id,
            'code'        => $u->unit_code,
            'product'     => $u->product->name,
            'serial'      => $u->serial_number ?: 'Belum diisi',
            'condition'   => $u->condition_status,
            'operational' => $u->operational_status,
            '_actions'    => [['label' => 'Ubah', 'variant' => 'soft', 'href' => route('admin.units.edit', $u)]],
        ])->all();

        return view('admin.inventory', ['rows' => $rows]);
    }

    public function create(): View
    {
        return view('admin.unit-form', ['unit' => new InventoryUnit(['condition_status' => 'GOOD', 'operational_status' => 'ACTIVE']), 'products' => $this->products()]);
    }

    public function store(InventoryUnitRequest $request): RedirectResponse
    {
        $unit = InventoryUnit::create($request->validated());

        return redirect()->route('admin.inventory')->with('status', 'Unit '.$unit->unit_code.' ditambahkan.');
    }

    public function edit(InventoryUnit $unit): View
    {
        return view('admin.unit-form', ['unit' => $unit, 'products' => $this->products()]);
    }

    public function update(InventoryUnitRequest $request, InventoryUnit $unit): RedirectResponse
    {
        $unit->update($request->validated());

        return redirect()->route('admin.inventory')->with('status', 'Unit '.$unit->unit_code.' diperbarui.');
    }

    private function products()
    {
        return Product::where('product_type', 'PHYSICAL')->orderBy('name')->get(['id', 'name', 'is_active']);
    }
}
