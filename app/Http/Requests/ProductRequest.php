<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validasi form produk admin (buat dan ubah). Otorisasi ditangani middleware rute (auth + admin). */
class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $current = $this->route('product');

        return [
            'name'              => ['required', 'string', 'min:2', 'max:180', Rule::unique('products', 'name')->ignore($current?->id)],
            'product_type'      => ['required', Rule::in(['PHYSICAL', 'DIGITAL_SERVICE'])],
            'description'       => ['nullable', 'string', 'max:5000'],
            'usage_guide'       => ['nullable', 'string', 'max:5000'],
            'specifications_text' => ['nullable', 'string', 'max:3000'],
            'rental_daily_rate' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'sale_price'        => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'shopee_url'        => ['nullable', 'url:https', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => 'Isi nama produk.',
            'name.unique'     => 'Nama produk ini sudah dipakai.',
            'product_type.in' => 'Pilih jenis produk.',
            'rental_daily_rate.numeric' => 'Harga sewa harus berupa angka.',
            'sale_price.numeric'        => 'Harga jual harus berupa angka.',
            'shopee_url.url'  => 'URL Shopee harus diawali https://',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama produk', 'rental_daily_rate' => 'harga sewa', 'sale_price' => 'harga jual'];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            if (! $this->boolean('rental_enabled') && ! $this->boolean('sale_enabled')) {
                $v->errors()->add('rental_enabled', 'Aktifkan minimal salah satu: disewakan atau dijual.');
            }

            [, $error] = Product::parseSpecifications($this->input('specifications_text'));
            if ($error) {
                $v->errors()->add('specifications_text', $error);
            }

            // Produk yang sudah punya unit tidak boleh diubah menjadi layanan digital.
            $current = $this->route('product');
            if ($current && $this->input('product_type') === 'DIGITAL_SERVICE' && $current->units()->exists()) {
                $v->errors()->add('product_type', 'Produk ini sudah punya unit fisik. Pensiunkan atau pindahkan unitnya terlebih dahulu.');
            }
        });
    }
}
