<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\InventoryUnit;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\RentalFine;
use App\Models\RentalInspection;
use App\Models\RentalOrder;
use App\Models\RentalWaitlistEntry;
use App\Models\Reservation;
use App\Models\User;
use App\Support\Availability;
use App\Support\InvoiceToken;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminController extends Controller
{
    private function ordersQuery()
    {
        return RentalOrder::with(['user', 'items', 'invoice', 'payment'])->latest('id');
    }

    private function orderRow(RentalOrder $order): array
    {
        $row = $order->toViewArray();
        $row['id'] = $order->id;
        $row['stack'] = [$row['customer'], $row['product'].' · '.$row['unit']];
        $row['due'] = $row['due_in'] < 0 ? 'Terlambat '.abs($row['due_in']).' hari' : ($row['due_in'] === 0 ? 'Berakhir hari ini' : $row['due_in'].' hari lagi');
        if ($row['pickup'] === 'PICKED_UP' && $row['rental'] === 'RENTING') {
            if ($row['due_in'] < 0) $row['rental'] = 'OVERDUE';
            elseif ($row['due_in'] <= 1) $row['rental'] = 'DUE_SOON';
        }
        return $row;
    }

    private function action(string $label, string $url, string $variant = 'primary', ?string $confirm = null, array $hidden = []): array
    {
        return ['label' => $label, 'variant' => $variant, 'form' => ['action' => $url, 'confirm' => $confirm, 'hidden' => $hidden]];
    }

    private function revenueByMonth(): array
    {
        $data = [];
        $now = CarbonImmutable::now(Availability::TZ)->startOfMonth();
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->subMonths($i);
            $start = $month->startOfMonth()->utc();
            $end = $month->endOfMonth()->endOfDay()->utc();
            $data[$month->locale('id')->translatedFormat('M')] = (float) Payment::where('payment_status', 'PAID')->whereNotNull('verified_at')
                ->whereBetween('verified_at', [$start, $end])->sum('amount');
        }
        return $data;
    }

    private function rentalRows($query): array
    {
        return $query->get()->map(fn (RentalOrder $order) => $this->orderRow($order))->all();
    }

    public function dashboard(): View
    {
        $now = CarbonImmutable::now('UTC');
        $sevenDays = $now->addDays(7);
        $activeUnits = InventoryUnit::where('operational_status', 'ACTIVE')
            ->whereHas('product', fn ($query) => $query->where('is_active', true)->where('rental_enabled', true))
            ->pluck('id');
        // Unit dihitung disewa hanya setelah pengambilan tercatat; reservasi yang belum diambil tetap terpisah.
        $baseReservations = Reservation::query()
            ->join('rental_order_items as roi', 'roi.id', '=', 'reservations.order_item_id')
            ->join('rental_orders as ro', 'ro.id', '=', 'roi.order_id')
            ->whereIn('reservations.unit_id', $activeUnits)
            ->whereIn('reservations.reservation_status', ['HOLD','CONFIRMED'])
            ->where('reservations.end_at', '>', $now);
        $rentedUnitIds = (clone $baseReservations)->where('ro.pickup_status', 'PICKED_UP')
            ->whereIn('ro.rental_status', ['RENTING','DUE_SOON','OVERDUE'])
            ->where('reservations.start_at', '<=', $now)->distinct()->pluck('reservations.unit_id');
        $reservedNowIds = (clone $baseReservations)->where('ro.pickup_status', 'NOT_PICKED_UP')
            ->where('ro.order_status', '!=', 'CANCELLED')
            ->where('reservations.start_at', '<=', $now)->distinct()->pluck('reservations.unit_id');
        $reservedUnitIds = (clone $baseReservations)->where('ro.pickup_status', 'NOT_PICKED_UP')
            ->where('ro.order_status', '!=', 'CANCELLED')
            ->whereBetween('reservations.start_at', [$now, $sevenDays])->distinct()->pluck('reservations.unit_id');
        $busyNowCount = $rentedUnitIds->merge($reservedNowIds)->unique()->count();
        $running = $this->ordersQuery()->where('pickup_status', 'PICKED_UP')->whereIn('rental_status', ['RENTING','DUE_SOON','OVERDUE'])->get();
        $runningRows = $running->map(fn (RentalOrder $o) => $this->orderRow($o));
        $alerts = $runningRows->filter(fn ($o) => in_array($o['rental'], ['DUE_SOON','OVERDUE'], true))->values()->all();
        $k = [
            'new_orders' => RentalOrder::whereIn('order_status', ['NEW_ORDER','WAITING_PAYMENT'])->count(),
            'ongoing' => $running->count(),
            'units_available' => max(0, $activeUnits->count() - $busyNowCount),
            'units_rented' => $rentedUnitIds->count(),
            'units_reserved' => $reservedUnitIds->count(),
            'unpaid' => Payment::where('payment_status', 'UNPAID')->count(),
            'paid_not_picked' => RentalOrder::where('payment_status', 'PAID')->where('pickup_status', 'NOT_PICKED_UP')->where('order_status', '!=', 'CANCELLED')->count(),
            'overdue' => $runningRows->where('rental', 'OVERDUE')->count(),
            'due_soon' => $runningRows->where('rental', 'DUE_SOON')->count(),
        ];
        $new = $this->rentalRows($this->ordersQuery()->whereIn('order_status', ['NEW_ORDER','WAITING_PAYMENT'])->limit(5));
        $ongoing = $runningRows->take(5)->all();
        return view('admin.dashboard', ['k' => $k, 'new' => $new, 'ongoing' => $ongoing, 'alerts' => $alerts, 'revenue' => $this->revenueByMonth()]);
    }

    public function newOrders(): View
    {
        $rows = $this->rentalRows($this->ordersQuery()->whereIn('order_status', ['NEW_ORDER','WAITING_PAYMENT']));
        foreach ($rows as &$row) $row['_actions'] = [['label' => 'Invoice', 'variant' => 'soft', 'href' => route('invoice.show', $row['invoice'])]];
        return view('admin.orders-new', ['rows' => $rows]);
    }

    public function ongoing(): View
    {
        $rows = $this->rentalRows($this->ordersQuery()->where('pickup_status', 'PICKED_UP')->whereIn('rental_status', ['RENTING','DUE_SOON','OVERDUE']));
        foreach ($rows as &$row) $row['_actions'] = [['label' => 'Invoice', 'variant' => 'soft', 'href' => route('invoice.show', $row['invoice'])]];
        return view('admin.rentals-ongoing', ['rows' => $rows]);
    }

    public function payments(): View
    {
        $payments = Payment::with(['order.user', 'order.items', 'order.invoice'])
            ->where('payment_status', 'UNPAID')
            ->latest('id')->get();
        return view('admin.payments', ['payments' => $payments]);
    }

    public function verifyPayment(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'amount_received' => ['required', 'numeric', 'gt:0', 'max:999999999999.99'],
        ]);
        $received = round((float) $data['amount_received'], 2);

        return DB::transaction(function () use ($request, $payment, $received) {
            $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $order = RentalOrder::whereKey($payment->order_id)->lockForUpdate()->firstOrFail();
            if ($payment->payment_status !== 'UNPAID' || $order->payment_status !== 'UNPAID') return back()->withErrors(['payment' => 'Pembayaran sudah diproses atau tidak lagi berstatus belum dibayar.']);
            if ($payment->payment_method !== 'COD' || $order->payment_method !== 'COD') return back()->withErrors(['payment' => 'Metode ini belum memiliki integrasi pembayaran yang tervalidasi.']);
            if ($order->order_status === 'CANCELLED') return back()->withErrors(['payment' => 'Pesanan telah dibatalkan. Pembayaran tidak dapat diverifikasi.']);

            // Jika tarif sudah tercatat, nominal diterima harus sama. Jika belum, admin memasukkan jumlah
            // uang yang benar-benar diterima sesuai kesepakatan mitra; sistem tidak menciptakan tarif.
            $expected = $order->total_amount ?? $payment->amount;
            if ($expected !== null && abs(((float) $expected) - $received) >= 0.01) {
                return back()->withErrors(['amount_received' => 'Nominal tidak sama dengan total pesanan yang sudah tercatat. Periksa invoice atau koreksi tarif melalui pengaturan yang disetujui.']);
            }

            $old = ['payment_status' => $order->payment_status, 'order_status' => $order->order_status, 'total_amount' => $order->total_amount];
            $payment->forceFill(['amount' => $received, 'payment_status' => 'PAID', 'verified_by' => $request->user()->id, 'verified_at' => now()])->save();
            $order->forceFill([
                'subtotal' => $order->subtotal ?? $received,
                'total_amount' => $order->total_amount ?? $received,
                'payment_status' => 'PAID',
                'order_status' => 'READY_FOR_PICKUP',
            ])->save();
            $invoice = Invoice::where('order_id', $order->id)->lockForUpdate()->first();
            if ($invoice) {
                $snapshot = $invoice->document_snapshot ?? [];
                $snapshot['total_amount'] = $order->total_amount;
                $snapshot['payment_verified_at'] = now()->toIso8601String();
                $invoice->forceFill(['document_snapshot' => $snapshot])->save();
            }
            AuditLog::record($request->user()->id, 'rental_order', $order->id, 'payment_verified', $old, [
                'payment_status' => 'PAID', 'order_status' => 'READY_FOR_PICKUP', 'amount_received' => $received,
            ]);
            return back()->with('status', 'Pembayaran COD sebesar Rp '.number_format($received, 0, ',', '.').' tercatat lunas. Status pengambilan belum berubah.');
        });
    }

    public function scanner(): View
    {
        $verifiedAt = (int) session('pitlabs_scanner_verified_at', 0);
        if ($verifiedAt < now()->subMinutes(5)->timestamp) {
            session()->forget(['pitlabs_scanner_result_id','pitlabs_scanner_mode','pitlabs_scanner_verified_at']);
            $id = null;
        } else {
            $id = session('pitlabs_scanner_result_id');
        }
        $order = $id ? RentalOrder::with(['user','items','invoice','payment'])->find($id) : null;
        return view('admin.scanner', ['scanOrder' => $order, 'scanMode' => session('pitlabs_scanner_mode')]);
    }

    public function scannerLookup(Request $request): RedirectResponse
    {
        // Hapus hasil scan sebelumnya terlebih dahulu agar payload gagal tidak menyisakan
        // pesanan lama yang masih dapat dikonfirmasi dari sesi admin.
        $request->session()->forget(['pitlabs_scanner_result_id','pitlabs_scanner_mode','pitlabs_scanner_verified_at']);
        $data = $request->validate(['scanner_input' => ['required','string','max:500']]);
        $raw = trim($data['scanner_input']);
        $invoiceNumber = $raw;
        $mode = 'manual';
        if (str_contains($raw, '|')) {
            $parsed = InvoiceToken::parse($raw);
            if (! $parsed) return back()->withErrors(['scanner_input' => 'Payload QR tidak valid atau tanda tangannya tidak cocok.']);
            [$invoiceNumber, $tokenHash] = $parsed;
            $mode = 'qr';
        }
        if (! preg_match('/^INV-[A-Z0-9-]{4,40}$/', $invoiceNumber)) return back()->withErrors(['scanner_input' => 'Masukkan nomor invoice yang valid atau pindai QR invoice PITLABS.']);
        $invoice = Invoice::with(['order.user','order.items','order.payment'])->where('invoice_number', $invoiceNumber)->first();
        if (! $invoice) return back()->withErrors(['scanner_input' => 'Invoice tidak ditemukan di database.']);
        if ($mode === 'qr' && ! hash_equals((string) $invoice->qr_token_hash, (string) $tokenHash)) return back()->withErrors(['scanner_input' => 'QR tidak cocok dengan token invoice yang tersimpan di database.']);
        $request->session()->put('pitlabs_scanner_result_id', $invoice->order_id);
        $request->session()->put('pitlabs_scanner_mode', $mode);
        $request->session()->put('pitlabs_scanner_verified_at', now()->timestamp);
        return redirect()->route('admin.scanner')->with('status', $mode === 'qr' ? 'QR valid dan invoice cocok dengan database. Periksa data sebelum mencatat pengambilan.' : 'Invoice ditemukan di database melalui pencarian manual. Periksa data sebelum mencatat pengambilan.');
    }

    public function confirmPickup(Request $request, RentalOrder $order): RedirectResponse
    {
        $verifiedAt = (int) $request->session()->get('pitlabs_scanner_verified_at', 0);
        abort_unless(
            (int) $request->session()->get('pitlabs_scanner_result_id') === (int) $order->id
            && $verifiedAt >= now()->subMinutes(5)->timestamp,
            403,
            'Invoice harus diverifikasi ulang melalui scanner. Verifikasi hanya berlaku selama lima menit.'
        );
        return DB::transaction(function () use ($request, $order) {
            $order = RentalOrder::with('invoice')->whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->order_status === 'CANCELLED' || $order->payment_status !== 'PAID') return back()->withErrors(['pickup' => 'Pesanan batal atau pembayaran belum lunas. Pengambilan ditolak.']);
            if ($order->pickup_status !== 'NOT_PICKED_UP' || $order->rental_status !== 'NOT_STARTED') return back()->withErrors(['pickup' => 'Pesanan sudah diambil atau tidak berada pada status yang dapat diproses.']);
            $old = ['pickup_status' => $order->pickup_status, 'rental_status' => $order->rental_status, 'order_status' => $order->order_status];
            $order->forceFill(['pickup_status' => 'PICKED_UP', 'rental_status' => 'RENTING', 'order_status' => 'PROCESSING', 'picked_up_at' => now()])->save();
            AuditLog::record($request->user()->id, 'rental_order', $order->id, 'pickup_confirmed', $old, ['pickup_status' => 'PICKED_UP','rental_status' => 'RENTING','order_status' => 'PROCESSING']);
            $request->session()->forget(['pitlabs_scanner_result_id','pitlabs_scanner_mode','pitlabs_scanner_verified_at']);
            return redirect()->route('admin.pickups')->with('status', 'Pengambilan tercatat. Status pembayaran tetap lunas.');
        });
    }

    public function pickups(): View
    {
        $rows = $this->rentalRows($this->ordersQuery()->where('payment_status','PAID')->where('pickup_status','NOT_PICKED_UP')->where('order_status','!=','CANCELLED'));
        foreach ($rows as &$row) $row['_actions'] = [['label' => 'Periksa melalui scanner', 'variant' => 'soft', 'href' => route('admin.scanner')]];
        return view('admin.pickups', ['rows' => $rows]);
    }

    public function receiveReturn(Request $request, RentalOrder $order): RedirectResponse
    {
        return DB::transaction(function () use ($request, $order) {
            $order = RentalOrder::with('items.reservation')->whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->pickup_status !== 'PICKED_UP' || ! in_array($order->rental_status, ['RENTING','DUE_SOON','OVERDUE'], true)) return back()->withErrors(['return' => 'Pesanan belum berada pada kondisi yang dapat dikembalikan.']);
            $old = ['rental_status' => $order->rental_status];
            $order->forceFill(['rental_status' => 'RETURNED', 'returned_at' => now()])->save();
            Reservation::whereIn('order_item_id', $order->items->pluck('id'))->whereIn('reservation_status', ['CONFIRMED','HOLD'])->update(['reservation_status' => 'COMPLETED']);

            // Unit tidak langsung dibuka untuk booking setelah diterima kembali.
            // Tahan di MAINTENANCE sampai pemeriksaan kondisi menyatakan GOOD.
            foreach ($order->items as $item) {
                if (! $item->unit_id) continue;
                $unit = InventoryUnit::whereKey($item->unit_id)->lockForUpdate()->first();
                if ($unit) $unit->forceFill(['operational_status' => 'MAINTENANCE'])->save();
            }

            AuditLog::record($request->user()->id, 'rental_order', $order->id, 'return_received', $old, [
                'rental_status' => 'RETURNED', 'units_held_for_inspection' => $order->items->pluck('unit_id')->filter()->values()->all(),
            ]);
            return back()->with('status', 'Pengembalian diterima. Pesanan masuk antrean pemeriksaan kondisi.');
        });
    }

    public function returns(): View
    {
        $rows = $this->rentalRows($this->ordersQuery()->where('pickup_status','PICKED_UP')->whereIn('rental_status',['RENTING','DUE_SOON','OVERDUE']));
        foreach ($rows as &$row) $row['_actions'] = [$this->action('Terima pengembalian', route('admin.returns.receive', $row['id']), 'primary', 'Pastikan perangkat dan kelengkapannya sudah diterima secara fisik.')];
        return view('admin.returns', ['rows' => $rows]);
    }

    public function inspections(): View
    {
        $cases = $this->ordersQuery()->whereIn('rental_status',['RETURNED','UNDER_INSPECTION'])->get()->map(function (RentalOrder $order) {
            $r = $this->orderRow($order);
            $r['inspection'] = RentalInspection::where('order_id', $order->id)->first();
            return $r;
        })->all();
        return view('admin.inspections', ['cases' => $cases]);
    }

    public function storeInspection(Request $request, RentalOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'condition_status' => ['required', Rule::in(['GOOD','MINOR_ISSUE','MAJOR_ISSUE','DAMAGED','LOST'])],
            'inspection_notes' => ['required','string','min:5','max:2000'],
            'completeness_notes' => ['nullable','string','max:1500'],
            'evidence' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
        ]);
        return DB::transaction(function () use ($request, $order, $data) {
            $order = RentalOrder::with('items')->whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (! in_array($order->rental_status, ['RETURNED','UNDER_INSPECTION'], true)) return back()->withErrors(['inspection' => 'Pengembalian belum diterima atau sudah ditutup.']);
            $item = $order->items->firstOrFail();
            $existing = RentalInspection::where('order_item_id', $item->id)->first();
            $evidencePath = $existing?->evidence_path;
            if ($request->hasFile('evidence')) $evidencePath = $request->file('evidence')->store('inspections', 'local');
            $inspection = RentalInspection::updateOrCreate(['order_item_id' => $item->id], [
                'order_id' => $order->id, 'unit_id' => $item->unit_id, 'inspector_user_id' => $request->user()->id,
                'condition_status' => $data['condition_status'], 'inspection_notes' => $data['inspection_notes'],
                'completeness_notes' => $data['completeness_notes'] ?? null, 'evidence_path' => $evidencePath, 'inspected_at' => now(),
            ]);
            if ($item->unit_id) {
                $unit = InventoryUnit::whereKey($item->unit_id)->lockForUpdate()->first();
                if ($unit) {
                    $operational = $data['condition_status'] === 'GOOD' ? 'ACTIVE' : ($data['condition_status'] === 'LOST' ? 'RETIRED' : 'MAINTENANCE');
                    $unit->forceFill(['condition_status' => $data['condition_status'], 'operational_status' => $operational])->save();
                }
            }
            if (in_array($data['condition_status'], ['DAMAGED','LOST'], true)) {
                RentalFine::firstOrCreate(['inspection_id' => $inspection->id], [
                    'order_id' => $order->id, 'fine_type' => $data['condition_status'] === 'LOST' ? 'LOSS' : 'DAMAGE',
                    'description' => 'Menunggu penilaian admin berdasarkan hasil pemeriksaan: '.$data['inspection_notes'],
                    'fine_status' => 'PENDING_REVIEW', 'amount' => null,
                ]);
            }
            $old = ['rental_status' => $order->rental_status];
            $order->forceFill(['rental_status' => 'COMPLETED', 'order_status' => 'COMPLETED'])->save();
            AuditLog::record($request->user()->id, 'rental_order', $order->id, 'inspection_completed', $old, ['rental_status' => 'COMPLETED','condition_status' => $data['condition_status']]);
            return redirect()->route('admin.inspections')->with('status', 'Pemeriksaan tersimpan. Kondisi dan status operasional unit telah diperbarui. Denda (bila ada) tetap menunggu keputusan nominal admin.');
        });
    }

    public function inspectionEvidence(RentalInspection $inspection): BinaryFileResponse
    {
        abort_unless($inspection->evidence_path && Storage::disk('local')->exists($inspection->evidence_path), 404);
        return response()->file(Storage::disk('local')->path($inspection->evidence_path));
    }

    public function fines(): View
    {
        $fines = RentalFine::with(['order.user','order.invoice','inspection'])->latest()->get();
        $orders = RentalOrder::with(['user','invoice'])->whereIn('rental_status',['RENTING','DUE_SOON','OVERDUE','RETURNED','COMPLETED'])->latest()->get();
        return view('admin.fines', ['fines' => $fines, 'orders' => $orders]);
    }

    public function storeFine(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_id' => ['required','integer','exists:rental_orders,id'],
            'fine_type' => ['required', Rule::in(['LATE','DAMAGE','LOSS','OTHER'])],
            'description' => ['required','string','min:5','max:2000'],
        ]);
        return DB::transaction(function () use ($request, $data) {
            $eligibleOrder = RentalOrder::whereKey($data['order_id'])->lockForUpdate()
                ->whereIn('rental_status', ['RENTING','DUE_SOON','OVERDUE','RETURNED','COMPLETED'])
                ->first();
            if (! $eligibleOrder) return back()->withErrors(['order_id' => 'Catatan denda hanya dapat dibuat untuk pesanan yang telah memasuki proses penyewaan atau pengembalian.']);
            $fine = RentalFine::create($data + ['fine_status' => 'PENDING_REVIEW', 'amount' => null]);
            AuditLog::record($request->user()->id, 'rental_fine', $fine->id, 'fine_created', null, ['order_id' => $fine->order_id, 'fine_type' => $fine->fine_type, 'fine_status' => $fine->fine_status]);
            return back()->with('status', 'Catatan denda dibuat tanpa nominal. Nominal hanya dapat dimasukkan setelah keputusan admin sesuai kebijakan mitra.');
        });
    }

    public function assessFine(Request $request, RentalFine $fine): RedirectResponse
    {
        $data = $request->validate(['amount' => ['required','numeric','gt:0','max:999999999999.99'], 'admin_note' => ['nullable','string','max:1500']]);
        return DB::transaction(function () use ($request, $fine, $data) {
            $fine = RentalFine::whereKey($fine->id)->lockForUpdate()->firstOrFail();
            if (! in_array($fine->fine_status, ['PENDING_REVIEW','ASSESSED'], true)) return back()->withErrors(['fine' => 'Denda tidak berada pada status yang dapat dinilai.']);
            $old = ['amount' => $fine->amount, 'fine_status' => $fine->fine_status];
            $fine->forceFill(['amount' => $data['amount'], 'admin_note' => $data['admin_note'] ?? $fine->admin_note, 'fine_status' => 'ASSESSED', 'assessed_by' => $request->user()->id, 'assessed_at' => now()])->save();
            AuditLog::record($request->user()->id, 'rental_fine', $fine->id, 'fine_assessed', $old, ['amount' => $fine->amount, 'fine_status' => 'ASSESSED']);
            return back()->with('status', 'Nominal denda dicatat sesuai keputusan admin. Tagihan belum ditandai lunas.');
        });
    }

    public function markFinePaid(Request $request, RentalFine $fine): RedirectResponse
    {
        $data = $request->validate(['amount_received' => ['required','numeric','gt:0','max:999999999999.99']]);
        $received = round((float) $data['amount_received'], 2);
        return DB::transaction(function () use ($request, $fine, $received) {
            $fine = RentalFine::whereKey($fine->id)->lockForUpdate()->firstOrFail();
            if ($fine->fine_status !== 'ASSESSED' || $fine->amount === null) return back()->withErrors(['fine' => 'Denda harus ditetapkan terlebih dahulu.']);
            if (abs(((float) $fine->amount) - $received) >= 0.01) return back()->withErrors(['amount_received' => 'Nominal yang diterima harus sama dengan denda yang telah ditetapkan.']);
            $fine->forceFill(['fine_status' => 'PAID', 'settled_by' => $request->user()->id, 'settled_at' => now()])->save();
            AuditLog::record($request->user()->id, 'rental_fine', $fine->id, 'fine_paid', ['fine_status' => 'ASSESSED'], ['fine_status' => 'PAID', 'amount' => $fine->amount, 'amount_received' => $received]);
            return back()->with('status', 'Pembayaran denda sebesar Rp '.number_format($received, 0, ',', '.').' tercatat diterima.');
        });
    }

    public function waiveFine(Request $request, RentalFine $fine): RedirectResponse
    {
        $data = $request->validate(['admin_note' => ['required','string','min:5','max:1500']]);
        return DB::transaction(function () use ($request, $fine, $data) {
            $fine = RentalFine::whereKey($fine->id)->lockForUpdate()->firstOrFail();
            if (! in_array($fine->fine_status, ['PENDING_REVIEW','ASSESSED'], true)) return back()->withErrors(['fine' => 'Denda tidak dapat dibebaskan pada status ini.']);
            $old = ['amount' => $fine->amount, 'fine_status' => $fine->fine_status];
            $fine->forceFill(['fine_status' => 'WAIVED', 'admin_note' => $data['admin_note'], 'assessed_by' => $request->user()->id, 'assessed_at' => now()])->save();
            AuditLog::record($request->user()->id, 'rental_fine', $fine->id, 'fine_waived', $old, ['fine_status' => 'WAIVED'], $data['admin_note']);
            return back()->with('status', 'Denda dibebaskan dan alasannya dicatat.');
        });
    }

    public function customers(): View
    {
        $rows = User::where('role', User::ROLE_CUSTOMER)->latest()->get()->map(function (User $u) {
            return ['id' => $u->id, 'stack' => [$u->name,$u->email], 'phone' => $u->phone ?: 'Belum diisi',
                'joined' => $u->created_at->timezone(Availability::TZ)->locale('id')->translatedFormat('j M Y'),
                'account' => $u->is_active ? 'ACTIVE' : 'INACTIVE',
                '_actions' => [$this->action($u->is_active ? 'Nonaktifkan' : 'Aktifkan', route('admin.customers.toggle',$u), 'soft', ($u->is_active ? 'Nonaktifkan' : 'Aktifkan').' akun '.$u->name.'?')]];
        })->all();
        return view('admin.customers', ['rows' => $rows]);
    }

    public function toggleCustomer(User $user): RedirectResponse
    {
        abort_unless($user->role === User::ROLE_CUSTOMER, 404);
        $old = ['is_active' => $user->is_active];
        $user->forceFill(['is_active' => ! $user->is_active])->save();
        AuditLog::record(request()->user()->id, 'user', $user->id, 'account_status_changed', $old, ['is_active' => $user->is_active]);
        return back()->with('status', 'Status akun '.$user->name.' diperbarui.');
    }

    public function waitlist(): View
    {
        $rows = RentalWaitlistEntry::with(['user','product'])->latest()->get()->map(function (RentalWaitlistEntry $entry) {
            $days = (int) $entry->start_at->diffInDays($entry->end_at);
            return ['id' => $entry->id, 'customer' => $entry->user?->name ?? '-', 'product' => $entry->product?->name ?? '-',
                'period' => $entry->start_at->timezone(Availability::TZ)->format('d/m/Y').' – '.$entry->end_at->timezone(Availability::TZ)->format('d/m/Y'),
                'days' => $days.' hari', 'status' => $entry->status,
                '_actions' => match ($entry->status) {
                    'WAITING' => [$this->action('Tandai sudah diberi tahu', route('admin.waitlist.status',$entry), 'soft', 'Catat bahwa pelanggan sudah dihubungi?', ['status' => 'NOTIFIED'])],
                    'NOTIFIED' => [
                        $this->action('Tandai terpenuhi', route('admin.waitlist.status',$entry), 'primary', 'Pastikan permintaan antrean sudah terpenuhi?', ['status' => 'FULFILLED']),
                        $this->action('Batalkan', route('admin.waitlist.status',$entry), 'outline-danger', 'Batalkan permintaan antrean ini?', ['status' => 'CANCELLED']),
                    ],
                    default => [],
                }];
        })->all();
        return view('admin.waitlist', ['rows' => $rows]);
    }

    public function waitlistStatus(Request $request, RentalWaitlistEntry $entry): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['NOTIFIED','FULFILLED','CANCELLED'])]]);
        return DB::transaction(function () use ($request, $entry, $data) {
            $entry = RentalWaitlistEntry::whereKey($entry->id)->lockForUpdate()->firstOrFail();
            if (! in_array($entry->status, ['WAITING','NOTIFIED'], true)) return back()->withErrors(['status' => 'Antrean yang sudah terpenuhi atau dibatalkan tidak dapat diubah kembali.']);
            if ($entry->status === 'NOTIFIED' && $data['status'] === 'NOTIFIED') return back()->withErrors(['status' => 'Pemberitahuan sudah dicatat.']);
            $old = ['status' => $entry->status];
            $entry->update(['status' => $data['status']]);
            AuditLog::record($request->user()->id, 'rental_waitlist', $entry->id, 'status_changed', $old, ['status' => $entry->status]);
            return back()->with('status', 'Status antrean diperbarui. Notifikasi eksternal tidak dikirim otomatis.');
        });
    }

    public function reports(Request $request): View|RedirectResponse
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $from = ! empty($filters['from']) ? CarbonImmutable::createFromFormat('!Y-m-d', $filters['from'], Availability::TZ)->startOfDay() : CarbonImmutable::now(Availability::TZ)->startOfMonth();
        $to = ! empty($filters['to']) ? CarbonImmutable::createFromFormat('!Y-m-d', $filters['to'], Availability::TZ)->endOfDay() : CarbonImmutable::now(Availability::TZ)->endOfDay();
        if ($to < $from) return back()->withErrors(['to' => 'Tanggal akhir harus sama atau setelah tanggal awal.']);
        $payments = Payment::where('payment_status','PAID')->whereNotNull('verified_at')->whereBetween('verified_at', [$from->utc(),$to->utc()]);
        $received = (float) (clone $payments)->sum('amount');
        $unpaidQuery = Payment::where('payment_status','UNPAID')->whereBetween('created_at', [$from->utc(),$to->utc()]);
        $unpaid = (float) (clone $unpaidQuery)->sum('amount');
        $unpricedUnpaid = (clone $unpaidQuery)->whereNull('amount')->count();
        $paidFine = (float) RentalFine::where('fine_status','PAID')->whereBetween('settled_at', [$from->utc(),$to->utc()])->sum('amount');
        $orders = $this->ordersQuery()->whereBetween('created_at', [$from->utc(),$to->utc()])->get();
        $rows = $orders->map(function (RentalOrder $o) {
            $row = $this->orderRow($o);
            $row['_actions'] = $o->invoice ? [['label' => 'Invoice', 'variant' => 'soft', 'href' => route('invoice.show', $o->invoice->invoice_number)]] : [];
            return $row;
        })->all();
        $monthly = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = CarbonImmutable::now(Availability::TZ)->startOfMonth()->subMonths($i);
            $monthly[$m->locale('id')->translatedFormat('M')] = (float) Payment::where('payment_status','PAID')->whereNotNull('verified_at')->whereBetween('verified_at', [$m->startOfMonth()->utc(),$m->endOfMonth()->endOfDay()->utc()])->sum('amount');
        }
        return view('admin.reports', ['received' => $received, 'unpaid' => $unpaid, 'unpricedUnpaid' => $unpricedUnpaid, 'paidFine' => $paidFine, 'revenue' => $monthly, 'rows' => $rows, 'from' => $from->toDateString(), 'to' => $to->toDateString()]);
    }
}
