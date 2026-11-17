<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Pengaturan sistem yang dapat diubah admin. Nilai kosong = belum ditetapkan. */
class AdminSettingsController extends Controller
{
    private const MAP = [
        'min_days'       => 'rental.min_days',
        'max_days'       => 'rental.max_days',
        'pickup_address' => 'contact.pickup_address',
        'pickup_hours'   => 'contact.pickup_hours',
    ];

    public function show(): View
    {
        $values = [];
        foreach (self::MAP as $field => $key) {
            $values[$field] = Setting::value($key);
        }

        return view('admin.settings', ['values' => $values]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'min_days'       => ['nullable', 'integer', 'min:1', 'max:365'],
            'max_days'       => ['nullable', 'integer', 'min:1', 'max:365', Rule::when($request->filled('min_days'), ['gte:min_days'])],
            'pickup_address' => ['nullable', 'string', 'max:300'],
            'pickup_hours'   => ['nullable', 'string', 'max:120'],
        ], [
            'min_days.integer' => 'Durasi minimum harus berupa angka bulat.',
            'min_days.min'     => 'Durasi minimum minimal 1 hari.',
            'max_days.gte'     => 'Durasi maksimum tidak boleh lebih kecil dari durasi minimum.',
            'max_days.integer' => 'Durasi maksimum harus berupa angka bulat.',
        ]);

        $old = $new = [];
        foreach (self::MAP as $field => $key) {
            $value = isset($data[$field]) && trim((string) $data[$field]) !== '' ? trim((string) $data[$field]) : null;
            $old[$key] = Setting::value($key);
            $new[$key] = $value;
            Setting::put($key, $value);
        }

        AuditLog::record($request->user()->id, 'settings', 0, 'updated', $old, $new);

        return back()->with('status', 'Pengaturan disimpan.');
    }
}
