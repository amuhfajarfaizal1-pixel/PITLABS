<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\MockData;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Halaman admin (Fase 4B) — TAMPILAN dengan data contoh dari MockData.
 * Tidak ada logika bisnis; aksi pada tabel hanyalah simulasi di browser.
 * Fase 5: ganti MockData dengan query model dan pindahkan aksi ke controller sungguhan.
 */
class AdminController extends Controller
{
    private function invoiceAction(array $o): array
    {
        return ['label' => 'Invoice', 'variant' => 'soft', 'href' => route('invoice.show', $o['invoice'])];
    }

    private function rows(array $orders, ?callable $actions = null, array $sim = []): array
    {
        return array_map(function (array $o) use ($actions, $sim) {
            $row = $o + ['stack' => [$o['customer'], $o['product'].' · '.$o['unit']]];
            $row['due'] = $o['due_in'] < 0
                ? 'Terlambat '.abs($o['due_in']).' hari'
                : ($o['due_in'] === 0 ? 'Berakhir hari ini' : 'Berakhir '.$o['due_in'].' hari lagi');
            if ($sim) {
                $row['_sim'] = $sim;
            }
            if ($actions) {
                $row['_actions'] = $actions($o);
            }

            return $row;
        }, $orders);
    }

    public function dashboard(): View
    {
        $view = fn (array $o) => [$this->invoiceAction($o)];

        return view('admin.dashboard', [
            'k'       => MockData::kpis(),
            'new'     => $this->rows(array_slice(MockData::newOrders(), 0, 5), $view),
            'ongoing' => $this->rows(array_slice(MockData::ongoing(), 0, 5), $view),
            'alerts'  => MockData::where(fn ($o) => in_array($o['rental'], ['OVERDUE', 'DUE_SOON'], true)),
            'revenue' => MockData::revenueByMonth(),
        ]);
    }

    public function newOrders(): View
    {
        return view('admin.orders-new', ['rows' => $this->rows(MockData::newOrders(), fn ($o) => [$this->invoiceAction($o)])]);
    }

    public function ongoing(): View
    {
        return view('admin.rentals-ongoing', ['rows' => $this->rows(MockData::ongoing(), fn ($o) => [$this->invoiceAction($o)])]);
    }

    public function payments(): View
    {
        $rows = $this->rows(MockData::unpaid(), function ($o) {
            if ($o['method'] !== 'COD') {
                return [];
            }

            return [['label' => 'Tandai lunas', 'variant' => 'primary', 'sim' => [
                'Konfirmasi pembayaran COD',
                $o['invoice'].' ('.$o['customer'].'): tandai LUNAS setelah uang diterima. Status pengambilan tidak ikut berubah.',
                'Pembayaran '.$o['invoice'].' ditandai lunas',
            ]]];
        }, ['payment' => 'PAID']);

        foreach ($rows as &$r) {
            $r['note'] = $r['method'] === 'COD' ? 'COD: konfirmasi manual setelah uang diterima' : 'Online: lunas otomatis setelah webhook gateway tervalidasi';
        }

        return view('admin.payments', ['rows' => $rows]);
    }

    public function scanner(): View
    {
        return view('admin.scanner', ['orders' => $this->rows(MockData::orders())]);
    }

    public function pickups(): View
    {
        $rows = $this->rows(MockData::readyForPickup(), fn ($o) => [['label' => 'Verifikasi pengambilan', 'variant' => 'primary', 'sim' => [
            'Verifikasi pengambilan',
            'Pastikan identitas '.$o['customer'].' sudah diperiksa dan QR/invoice '.$o['invoice'].' valid. Status pembayaran tidak ikut berubah.',
            'Pengambilan '.$o['invoice'].' tercatat',
        ]]], ['pickup' => 'PICKED_UP']);

        return view('admin.pickups', ['rows' => $rows]);
    }

    public function returns(): View
    {
        $due = $this->rows(MockData::ongoing(), fn ($o) => [['label' => 'Terima pengembalian', 'variant' => 'primary', 'sim' => [
            'Terima pengembalian',
            $o['product'].' ('.$o['unit'].') dikembalikan oleh '.$o['customer'].'. Status penyewaan menjadi Sudah dikembalikan lalu menunggu pemeriksaan.',
            'Pengembalian '.$o['invoice'].' diterima',
        ]]], ['rental' => 'RETURNED']);

        $inspect = $this->rows(MockData::underInspection(), fn ($o) => [['label' => 'Periksa kondisi', 'variant' => 'soft', 'href' => route('admin.inspections')]]);

        return view('admin.returns', ['rows' => array_merge($due, $inspect)]);
    }

    public function inspections(): View
    {
        return view('admin.inspections', ['cases' => MockData::underInspection()]);
    }

    public function fines(): View
    {
        $rows = [
            ['id' => 1, 'invoice' => 'INV-CONTOH-003', 'customer' => 'Budi Santoso', 'kind' => 'Keterlambatan', 'basis' => 'Melewati batas sewa; dihitung dari tarif yang disahkan mitra', 'amount' => null, 'decision' => 'Menunggu keputusan admin'],
            ['id' => 2, 'invoice' => 'INV-CONTOH-009', 'customer' => 'Lina Marlina', 'kind' => 'Kerusakan ringan', 'basis' => 'Hasil pemeriksaan kondisi saat pengembalian', 'amount' => null, 'decision' => 'Ditetapkan admin'],
        ];

        return view('admin.fines', ['rows' => $rows]);
    }

    /** Daftar pelanggan dari DATABASE (Fase 5A). */
    public function customers(): View
    {
        $rows = User::where('role', User::ROLE_CUSTOMER)->latest()->get()->map(function (User $u) {
            $on = $u->is_active;

            return [
                'id'      => $u->id,
                'stack'   => [$u->name, $u->email],
                'phone'   => $u->phone ?: 'Belum diisi',
                'joined'  => $u->created_at->timezone('Asia/Makassar')->locale('id')->translatedFormat('j M Y'),
                'account' => $on ? 'ACTIVE' : 'INACTIVE',
                '_actions' => [['label' => $on ? 'Nonaktifkan' : 'Aktifkan', 'variant' => 'soft',
                    'form' => ['action' => route('admin.customers.toggle', $u), 'confirm' => ($on ? 'Nonaktifkan' : 'Aktifkan').' akun '.$u->name.'?']]],
            ];
        })->all();

        return view('admin.customers', ['rows' => $rows]);
    }

    public function toggleCustomer(User $user): RedirectResponse
    {
        abort_unless($user->role === User::ROLE_CUSTOMER, 404);

        $user->forceFill(['is_active' => ! $user->is_active])->save();

        return back()->with('status', 'Akun '.$user->name.($user->is_active ? ' diaktifkan.' : ' dinonaktifkan.'));
    }

    public function waitlist(): View
    {
        return view('admin.waitlist', ['rows' => MockData::waitlist()]);
    }

    public function reports(): View
    {
        $orders = MockData::orders();
        $received = array_sum(array_column(array_filter($orders, fn ($o) => $o['payment'] === 'PAID'), 'total'));
        $unpaid = array_sum(array_column(array_filter($orders, fn ($o) => $o['payment'] === 'UNPAID' && $o['order'] !== 'CANCELLED'), 'total'));

        return view('admin.reports', [
            'received' => $received,
            'unpaid'   => $unpaid,
            'revenue'  => MockData::revenueByMonth(),
            'rows'     => $this->rows(array_filter($orders, fn ($o) => $o['order'] !== 'CANCELLED')),
        ]);
    }
}
