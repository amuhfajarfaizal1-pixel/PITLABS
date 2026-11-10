<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Support\MockAvailability;
use App\Support\MockCatalog;
use App\Support\MockData;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Halaman pelanggan (Fase 4B) — TAMPILAN dengan data contoh.
 * Checkout hanya membuat "pesanan simulasi" di session; tidak ada database.
 */
class CustomerController extends Controller
{
    /** Data pengguna untuk view (array agar view tidak bergantung pada bentuk model). */
    private function userData(): array
    {
        $u = Auth::user();

        return ['name' => $u->name, 'email' => $u->email, 'phone' => $u->phone, 'provider' => $u->google_id ? 'google' : 'password'];
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name'  => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
        ], [
            'name.required' => 'Isi nama lengkap Anda.',
            'name.min'      => 'Nama minimal 3 karakter.',
            'email.unique'  => 'Email ini sudah dipakai akun lain.',
            'phone.regex'   => 'Nomor WhatsApp tidak valid. Contoh: 081234567890',
        ]);

        $user->update($data);

        return back()->with('status', 'Profil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password'          => ['required', 'current_password'],
            'new_password'              => ['required', 'string', 'min:10', 'max:255', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required'         => 'Isi kata sandi saat ini.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'new_password.min'                  => 'Kata sandi baru minimal 10 karakter.',
            'new_password.confirmed'            => 'Konfirmasi kata sandi tidak sama.',
            'new_password.different'            => 'Kata sandi baru harus berbeda dari yang lama.',
        ]);

        $request->user()->update(['password' => $data['new_password']]);

        return back()->with('status', 'Kata sandi diubah.');
    }

    private function orders(): array
    {
        return array_map(function (array $o) {
            $o['due_label'] = $o['due_in'] < 0 ? 'Terlambat '.abs($o['due_in']).' hari'
                : ($o['due_in'] === 0 ? 'Berakhir hari ini' : $o['due_in'].' hari lagi');
            $total = max(1, (int) $o['start']->diffInDays($o['end']));
            $done = (int) $o['start']->diffInDays(MockAvailability::today(), false);
            $o['progress'] = max(0, min(100, (int) round($done / $total * 100)));

            return $o;
        }, MockData::forCustomer());
    }

    public function dashboard(): View
    {
        $orders = $this->orders();

        return view('dashboard.index', [
            'user'      => $this->userData(),
            'active'    => array_values(array_filter($orders, fn ($o) => in_array($o['rental'], ['RENTING', 'DUE_SOON', 'OVERDUE'], true))),
            'upcoming'  => array_values(array_filter($orders, fn ($o) => $o['rental'] === 'NOT_STARTED' && $o['order'] !== 'CANCELLED')),
            'history'   => $orders,
            'reminders' => MockData::reminders(),
        ]);
    }

    public function rentals(): View
    {
        $orders = $this->orders();

        return view('dashboard.rentals', [
            'active'   => array_values(array_filter($orders, fn ($o) => in_array($o['rental'], ['RENTING', 'DUE_SOON', 'OVERDUE'], true))),
            'upcoming' => array_values(array_filter($orders, fn ($o) => $o['rental'] === 'NOT_STARTED' && $o['order'] !== 'CANCELLED')),
        ]);
    }

    public function history(): View
    {
        $rows = array_map(fn ($o) => $o + [
            'stack'    => [$o['product'], $o['unit']],
            '_actions' => [['label' => 'Invoice', 'variant' => 'soft', 'href' => route('invoice.show', $o['invoice'])]],
        ], $this->orders());

        return view('dashboard.history', ['rows' => $rows]);
    }

    public function waitlist(): View
    {
        $mine = array_values(array_filter(MockData::waitlist(), fn ($w) => $w['customer'] === 'Pelanggan Demo'));
        $added = session('pit_demo_waitlist', []);

        return view('dashboard.waitlist', ['rows' => array_merge($added, $mine)]);
    }

    /** Simulasi: masuk antrean saat semua unit bentrok. Fase 5: simpan ke tabel antrean. */
    public function waitlistStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'produk' => ['required', 'string', 'max:100'],
            'mulai'  => ['required', 'date_format:Y-m-d'],
            'durasi' => ['required', 'integer', 'min:1', 'max:90'],
            'satuan' => ['required', 'in:hari,minggu,bulan'],
        ]);

        $product = MockCatalog::find($data['produk']) ?? abort(422);
        $start = CarbonImmutable::createFromFormat('Y-m-d', $data['mulai'], MockAvailability::TZ)->startOfDay();
        $n = (int) $data['durasi'];
        $end = match ($data['satuan']) {
            'minggu' => $start->addWeeks($n),
            'bulan'  => $start->addMonthsNoOverflow($n),
            default  => $start->addDays($n),
        };
        $fmt = fn (CarbonImmutable $d) => $d->locale('id')->translatedFormat('j M');

        $list = session('pit_demo_waitlist', []);
        array_unshift($list, [
            'id' => 100 + count($list), 'customer' => 'Pelanggan Demo', 'product' => $product['name'],
            'period' => $fmt($start).' – '.$fmt($end), 'days' => (int) $start->diffInDays($end).' hari', 'status' => 'WAITING',
        ]);
        session(['pit_demo_waitlist' => $list]);

        return redirect()->route('dashboard.waitlist')->with('status', 'Anda masuk antrean (simulasi). Kami memberi tahu saat unit tersedia.');
    }

    public function returns(): View
    {
        return view('dashboard.returns', ['active' => array_values(array_filter($this->orders(), fn ($o) => in_array($o['rental'], ['RENTING', 'DUE_SOON', 'OVERDUE'], true)))]);
    }

    public function profile(): View
    {
        return view('dashboard.profile', ['user' => $this->userData()]);
    }

    /* ---------- Checkout (simulasi) ---------- */

    public function checkout(Request $request): View
    {
        $products = array_values(array_filter(MockCatalog::all(), fn ($p) => $p['rental_enabled']));
        $units = null;
        $summary = null;

        $v = Validator::make($request->query(), [
            'produk' => ['required', 'in:'.implode(',', array_column($products, 'slug'))],
            'mulai'  => ['required', 'date_format:Y-m-d'],
            'durasi' => ['required', 'integer', 'min:1', 'max:90'],
            'satuan' => ['required', 'in:hari,minggu,bulan'],
        ]);

        if (! $v->fails()) {
            $start = CarbonImmutable::createFromFormat('Y-m-d', $request->query('mulai'), MockAvailability::TZ)->startOfDay();
            if ($start >= MockAvailability::today()) {
                $n = (int) $request->query('durasi');
                $end = match ($request->query('satuan')) {
                    'minggu' => $start->addWeeks($n),
                    'bulan'  => $start->addMonthsNoOverflow($n),
                    default  => $start->addDays($n),
                };
                $units = MockAvailability::check($request->query('produk'), $start, $end);
                $summary = [
                    'product' => MockCatalog::find($request->query('produk')),
                    'start'   => $start->locale('id')->translatedFormat('j F Y'),
                    'end'     => $end->locale('id')->translatedFormat('j F Y'),
                    'days'    => (int) $start->diffInDays($end),
                ];
            }
        }

        return view('checkout', ['products' => $products, 'units' => $units, 'summary' => $summary]);
    }

    public function checkoutStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'produk'         => ['required', 'string', 'max:100'],
            'mulai'          => ['required', 'date_format:Y-m-d'],
            'durasi'         => ['required', 'integer', 'min:1', 'max:90'],
            'satuan'         => ['required', 'in:hari,minggu,bulan'],
            'unit'           => ['required', 'string', 'max:20'],
            'metode'         => ['required', 'in:QRIS,VA,COD'],
            'terms_accepted' => ['accepted'],
        ], [
            'unit.required'           => 'Pilih unit yang tersedia.',
            'metode.required'         => 'Pilih metode pembayaran.',
            'metode.in'               => 'Metode pembayaran tidak valid.',
            'terms_accepted.accepted' => 'Anda harus menyetujui ketentuan sewa dan denda kerusakan/kehilangan.',
        ]);

        $product = MockCatalog::find($data['produk']) ?? abort(422);
        $start = CarbonImmutable::createFromFormat('Y-m-d', $data['mulai'], MockAvailability::TZ)->startOfDay();
        $n = (int) $data['durasi'];
        $end = match ($data['satuan']) {
            'minggu' => $start->addWeeks($n),
            'bulan'  => $start->addMonthsNoOverflow($n),
            default  => $start->addDays($n),
        };
        $fmt = fn (CarbonImmutable $d) => $d->locale('id')->translatedFormat('j M');
        $cod = $data['metode'] === 'COD';

        // Invoice dibuat sejak pesanan terbentuk, termasuk COD (BUSINESS_RULES §4, §9).
        session(['pit_demo_order' => [
            'id'       => 0,
            'invoice'  => 'INV-DEMO-'.now(MockAvailability::TZ)->format('ymd-His'),
            'customer' => Auth::user()->name,
            'product'  => $product['name'],
            'unit'     => $data['unit'],
            'period'   => $fmt($start).' – '.$fmt($end),
            'start_full' => $start->locale('id')->translatedFormat('j F Y'),
            'end_full'   => $end->locale('id')->translatedFormat('j F Y'),
            'days'     => (int) $start->diffInDays($end),
            'method'   => $data['metode'] === 'VA' ? 'Virtual account' : $data['metode'],
            'total'    => null, // tarif belum ditetapkan mitra
            'order'    => $cod ? 'WAITING_PAYMENT' : 'NEW_ORDER',
            'payment'  => 'UNPAID',
            'pickup'   => 'NOT_PICKED_UP',
            'rental'   => 'NOT_STARTED',
        ]]);

        return redirect()->route('invoice.show', 'baru')
            ->with('status', 'Pesanan simulasi dibuat dan invoice terbit. Pesanan belum disimpan ke database.');
    }

    /* ---------- Invoice ---------- */

    public function invoice(string $number): View
    {
        $order = $number === 'baru' ? session('pit_demo_order') : MockData::order($number);
        abort_if(! $order, 404);

        $order['start_full'] ??= $order['start']->locale('id')->translatedFormat('j F Y');
        $order['end_full']   ??= $order['end']->locale('id')->translatedFormat('j F Y');
        $order['days']       ??= (int) $order['start']->diffInDays($order['end']);

        return view('invoice', [
            'o'      => $order,
            'qr'     => \App\Support\Pit::qrPlaceholder($order['invoice']),
            'issued' => MockAvailability::today()->locale('id')->translatedFormat('j F Y'),
        ]);
    }
}
