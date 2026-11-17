<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pengaturan yang dapat diubah admin. Nilai kosong (null) = belum ditetapkan.
 * Kunci yang dipakai: lihat self::KEYS.
 */
class Setting extends Model
{
    public const KEYS = [
        'rental.min_days'       => 'Durasi sewa minimum (hari)',
        'rental.max_days'       => 'Durasi sewa maksimum (hari)',
        'contact.pickup_address' => 'Alamat lokasi pengambilan',
        'contact.pickup_hours'  => 'Jam operasional pengambilan',
    ];

    protected $fillable = ['setting_key', 'setting_value'];

    public static function value(string $key, ?string $default = null): ?string
    {
        $row = static::where('setting_key', $key)->first();
        $v = $row?->setting_value;

        return ($v === null || $v === '') ? $default : $v;
    }

    public static function intValue(string $key): ?int
    {
        $v = static::value($key);

        return $v === null ? null : (int) $v;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['setting_key' => $key], ['setting_value' => ($value === '' ? null : $value)]);
    }
}
