<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validasi form unit inventaris admin. Unit hanya untuk produk perangkat fisik. */
class InventoryUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('unit');

        return [
            'product_id'         => ['required', Rule::exists('products', 'id')->where('product_type', 'PHYSICAL')],
            'unit_code'          => ['required', 'string', 'max:80', 'regex:/^[A-Z0-9][A-Z0-9-]{2,78}$/', Rule::unique('inventory_units', 'unit_code')->ignore($current?->id)],
            'serial_number'      => ['nullable', 'string', 'max:120'],
            'condition_status'   => ['required', Rule::in(['GOOD', 'MINOR_ISSUE', 'MAJOR_ISSUE', 'DAMAGED', 'LOST'])],
            'operational_status' => ['required', Rule::in(['ACTIVE', 'MAINTENANCE', 'RETIRED'])],
            'notes'              => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Pilih produk perangkat fisik.',
            'product_id.exists'   => 'Unit hanya dapat dibuat untuk produk perangkat fisik.',
            'unit_code.required'  => 'Isi kode unit.',
            'unit_code.regex'     => 'Kode unit hanya huruf besar, angka, dan tanda minus (3–79 karakter). Contoh: JON-004',
            'unit_code.unique'    => 'Kode unit ini sudah dipakai.',
        ];
    }
}
