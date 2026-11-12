<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    /** Ikon tampilan per slug resmi; produk baru memakai ikon default sesuai jenis. */
    private const ICONS = [
        'laptop' => 'bi-laptop', 'raspberry-pi-5' => 'bi-motherboard', 'jetson-orin-nano' => 'bi-cpu',
        'drone' => 'bi-airplane', 'kamera-mirrorless' => 'bi-camera',
        'google-colab-pro' => 'bi-cloud', 'google-colab-pro-plus' => 'bi-cloud-plus',
    ];

    protected $fillable = [
        'category_id', 'name', 'slug', 'product_type', 'description', 'specifications', 'usage_guide',
        'sale_enabled', 'rental_enabled', 'sale_price', 'rental_daily_rate', 'shopee_url', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'specifications'    => 'array',
            'sale_enabled'      => 'boolean',
            'rental_enabled'    => 'boolean',
            'is_active'         => 'boolean',
            'sale_price'        => 'decimal:2',
            'rental_daily_rate' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(InventoryUnit::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function isDigital(): bool
    {
        return $this->product_type === 'DIGITAL_SERVICE';
    }

    /** Bentuk array yang dipakai view katalog (sama dengan data contoh sebelumnya). */
    public function toViewArray(): array
    {
        return [
            'id'                => $this->id,
            'slug'              => $this->slug,
            'name'              => $this->name,
            'type'              => $this->isDigital() ? 'digital' : 'physical',
            'icon'              => self::ICONS[$this->slug] ?? ($this->isDigital() ? 'bi-cloud' : 'bi-box-seam'),
            'description'       => $this->description,
            'specifications'    => $this->specifications ?? [],
            'usage_guide'       => $this->usage_guide,
            'sale_enabled'      => $this->sale_enabled,
            'rental_enabled'    => $this->rental_enabled,
            'sale_price'        => $this->sale_price !== null ? (int) $this->sale_price : null,
            'rental_daily_rate' => $this->rental_daily_rate !== null ? (int) $this->rental_daily_rate : null,
            'shopee_url'        => $this->shopee_url,
        ];
    }

    /** Slug unik dari nama: "Nama Produk" → nama-produk, nama-produk-2, … */
    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        for ($i = 2; static::where('slug', $slug)->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    /**
     * Teks spesifikasi "Label: nilai" per baris → array.
     * @return array{0: array<string,string>, 1: ?string} [hasil, pesan error]
     */
    public static function parseSpecifications(?string $text): array
    {
        $out = [];
        foreach (preg_split('/\R/', trim((string) $text)) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (! str_contains($line, ':')) {
                return [[], "Baris \"{$line}\" harus berformat \"Label: nilai\"."];
            }
            [$label, $value] = array_map('trim', explode(':', $line, 2));
            if ($label === '' || $value === '' || mb_strlen($label) > 60 || mb_strlen($value) > 200) {
                return [[], "Baris \"{$line}\": label maksimal 60 dan nilai maksimal 200 karakter, keduanya wajib diisi."];
            }
            $out[$label] = $value;
        }

        return count($out) > 30 ? [[], 'Maksimal 30 baris spesifikasi.'] : [$out, null];
    }
}
