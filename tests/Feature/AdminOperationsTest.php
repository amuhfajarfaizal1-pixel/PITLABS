<?php
namespace Tests\Feature;

use App\Models\InventoryUnit;
use App\Models\Payment;
use App\Models\Product;
use App\Models\RentalFine;
use App\Models\RentalOrder;
use App\Models\RentalWaitlistEntry;
use App\Models\User;
use App\Support\Availability;
use App\Support\InvoiceToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOperationsTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $email, string $role = 'CUSTOMER'): User
    {
        return User::unguarded(fn () => User::create(['name' => 'Pengguna Uji', 'email' => $email, 'password' => 'kata-sandi-aman-123', 'role' => $role, 'is_active' => true]));
    }

    private function bookedOrder(): RentalOrder
    {
        $product = Product::create(['name' => 'Perangkat uji', 'slug' => 'perangkat-uji', 'product_type' => 'PHYSICAL', 'rental_enabled' => true, 'is_active' => true, 'rental_daily_rate' => 125000]);
        InventoryUnit::create(['product_id' => $product->id, 'unit_code' => 'UNIT-UJI-01']);
        $customer = $this->user('customer-ops@example.test');
        $start = Availability::today()->addDays(2);
        $this->actingAs($customer)->post('/checkout', ['produk'=>'perangkat-uji','mulai'=>$start->format('Y-m-d'),'durasi'=>2,'satuan'=>'hari','unit'=>'UNIT-UJI-01','metode'=>'COD','terms_accepted'=>'1'])->assertRedirect();
        return RentalOrder::with(['invoice','payment','items'])->firstOrFail();
    }

    public function test_admin_can_verify_cod_scan_invoice_pickup_return_and_inspect(): void
    {
        $order = $this->bookedOrder();
        $admin = $this->user('admin-ops@example.test', 'ADMIN');
        $payment = Payment::where('order_id', $order->id)->firstOrFail();
        $this->actingAs($admin)->post(route('admin.payments.verify', $payment), ['amount_received' => 250000])->assertRedirect();
        $order->refresh();
        $this->assertSame('PAID', $order->payment_status);
        $this->assertSame('NOT_PICKED_UP', $order->pickup_status);
        $this->assertSame('NOT_STARTED', $order->rental_status);

        $payload = InvoiceToken::payload($order->invoice->invoice_number);
        $this->post(route('admin.scanner.lookup'), ['scanner_input' => $payload])->assertRedirect(route('admin.scanner'));
        $this->assertSame($order->id, session('pitlabs_scanner_result_id'));
        $this->post(route('admin.pickups.confirm', $order))->assertRedirect(route('admin.pickups'));
        $order->refresh();
        $this->assertSame('PICKED_UP', $order->pickup_status);
        $this->assertSame('RENTING', $order->rental_status);

        $this->post(route('admin.returns.receive', $order))->assertRedirect();
        $this->assertSame('MAINTENANCE', InventoryUnit::where('unit_code', 'UNIT-UJI-01')->firstOrFail()->operational_status, 'Unit harus tetap ditahan sampai inspeksi selesai.');
        $order->refresh();
        $this->assertSame('RETURNED', $order->rental_status);
        $this->post(route('admin.inspections.store', $order), ['condition_status'=>'DAMAGED','inspection_notes'=>'Retak pada bagian casing perangkat','completeness_notes'=>'Unit dan kabel diterima'])->assertRedirect(route('admin.inspections'));
        $order->refresh();
        $this->assertSame('COMPLETED', $order->rental_status);
        $this->assertDatabaseHas('rental_inspections', ['order_id'=>$order->id,'condition_status'=>'DAMAGED']);
        $fine = RentalFine::where('order_id',$order->id)->firstOrFail();
        $this->assertNull($fine->amount);
        $this->assertSame('PENDING_REVIEW', $fine->fine_status);
        $this->actingAs($admin)->post(route('admin.fines.assess',$fine), ['amount'=>50000,'admin_note'=>'Nominal mengikuti keputusan mitra yang telah disetujui'])->assertRedirect();
        $fine->refresh();
        $this->assertSame('ASSESSED', $fine->fine_status);
        $this->from(route('admin.fines'))->post(route('admin.fines.paid',$fine), ['amount_received'=>49000])->assertSessionHasErrors('amount_received');
        $this->assertSame('ASSESSED', $fine->fresh()->fine_status);
        $this->post(route('admin.fines.paid',$fine), ['amount_received'=>50000])->assertRedirect();
        $this->assertSame('PAID', $fine->fresh()->fine_status);
    }

    public function test_guest_clicking_product_detail_is_redirected_to_login_and_returns_after_login(): void
    {
        $product = Product::create(['name'=>'Produk privat','slug'=>'produk-privat','product_type'=>'PHYSICAL','rental_enabled'=>true,'is_active'=>true]);
        $this->get('/produk/produk-privat')->assertRedirect('/masuk');
        $user = $this->user('detail-user@example.test');
        $this->post('/masuk', ['email'=>$user->email,'password'=>'kata-sandi-aman-123'])->assertRedirect('/produk/produk-privat');
    }

    public function test_payment_verification_is_rejected_without_an_entered_amount(): void
    {
        $product = Product::create(['name'=>'Tanpa tarif','slug'=>'tanpa-tarif','product_type'=>'PHYSICAL','rental_enabled'=>true,'is_active'=>true]);
        InventoryUnit::create(['product_id'=>$product->id,'unit_code'=>'NO-RATE-01']);
        $customer = $this->user('no-rate@example.test');
        $start = Availability::today()->addDays(3);
        $this->actingAs($customer)->post('/checkout',['produk'=>'tanpa-tarif','mulai'=>$start->format('Y-m-d'),'durasi'=>1,'satuan'=>'hari','unit'=>'NO-RATE-01','metode'=>'COD','terms_accepted'=>'1']);
        $order = RentalOrder::firstOrFail();
        $payment = Payment::where('order_id',$order->id)->firstOrFail();
        $admin = $this->user('admin-no-rate@example.test','ADMIN');
        $this->actingAs($admin)->post(route('admin.payments.verify',$payment), [])->assertSessionHasErrors('amount_received');
        $this->assertSame('UNPAID',$payment->fresh()->payment_status);
    }
    public function test_admin_can_record_received_cod_amount_when_partner_tariff_is_not_configured(): void
    {
        $product = Product::create(['name'=>'Tanpa tarif','slug'=>'tanpa-tarif-berbayar','product_type'=>'PHYSICAL','rental_enabled'=>true,'is_active'=>true]);
        InventoryUnit::create(['product_id'=>$product->id,'unit_code'=>'NO-RATE-PAID-01']);
        $customer = $this->user('no-rate-paid@example.test');
        $start = Availability::today()->addDays(4);
        $this->actingAs($customer)->post('/checkout',['produk'=>'tanpa-tarif-berbayar','mulai'=>$start->format('Y-m-d'),'durasi'=>2,'satuan'=>'hari','unit'=>'NO-RATE-PAID-01','metode'=>'COD','terms_accepted'=>'1'])->assertRedirect();
        $order = RentalOrder::with('invoice')->firstOrFail();
        $payment = Payment::where('order_id',$order->id)->firstOrFail();
        $admin = $this->user('no-rate-paid-admin@example.test','ADMIN');

        $this->actingAs($admin)->post(route('admin.payments.verify',$payment), ['amount_received'=>175000])->assertRedirect();

        $this->assertDatabaseHas('rental_orders', ['id'=>$order->id,'payment_status'=>'PAID','total_amount'=>'175000.00']);
        $this->assertDatabaseHas('payments', ['id'=>$payment->id,'payment_status'=>'PAID','amount'=>'175000.00']);
        $this->assertEquals(175000, (float) $order->invoice()->firstOrFail()->document_snapshot['total_amount']);
    }

    public function test_invalid_qr_does_not_leave_a_previous_verified_invoice_in_session(): void
    {
        $order = $this->bookedOrder();
        $admin = $this->user('admin-scanner-guard@example.test', 'ADMIN');
        $this->actingAs($admin);
        session(['pitlabs_scanner_result_id' => $order->id, 'pitlabs_scanner_mode' => 'qr']);

        $payload = InvoiceToken::payload($order->invoice->invoice_number);
        $segments = explode('|', $payload);
        $segments[count($segments) - 1] = str_repeat('0', strlen(end($segments)));
        $invalidPayload = implode('|', $segments);

        $this->post(route('admin.scanner.lookup'), ['scanner_input' => $invalidPayload])->assertSessionHasErrors('scanner_input');
        $this->assertNull(session('pitlabs_scanner_result_id'));
        $this->assertNull(session('pitlabs_scanner_mode'));
    }

    public function test_pickup_requires_a_recent_scanner_verification(): void
    {
        $order = $this->bookedOrder();
        $admin = $this->user('admin-scanner-expiry@example.test', 'ADMIN');
        $this->actingAs($admin)->withSession([
            'pitlabs_scanner_result_id' => $order->id,
            'pitlabs_scanner_mode' => 'qr',
            'pitlabs_scanner_verified_at' => now()->subMinutes(6)->timestamp,
        ])->post(route('admin.pickups.confirm',$order))->assertForbidden();
        $this->assertSame('NOT_PICKED_UP', $order->fresh()->pickup_status);
    }

    public function test_pickup_cannot_be_confirmed_before_an_invoice_was_scanned(): void
    {
        $order = $this->bookedOrder();
        $admin = $this->user('admin-pickup-guard@example.test', 'ADMIN');
        $this->actingAs($admin)->post(route('admin.pickups.confirm',$order))->assertForbidden();
        $this->assertSame('NOT_PICKED_UP', $order->fresh()->pickup_status);
    }

}
