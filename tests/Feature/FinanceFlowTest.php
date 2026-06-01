<?php

namespace Tests\Feature;

use App\Livewire\Finance\EVouchers\Create as EVoucherCreate;
use App\Livewire\Finance\Invoices\Show as InvoiceShow;
use App\Livewire\Finance\PurchaseOrders\Create as PurchaseOrderCreate;
use App\Livewire\Finance\PurchaseOrders\Show as PurchaseOrderShow;
use App\Models\Booking;
use App\Models\Client;
use App\Models\EVoucher;
use App\Models\Invoice;
use App\Models\Pool;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Services\Finance\FinanceFlowService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_finance_user_can_view_po_index(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();

        $this->actingAs($user)->get('/finance/purchase-orders')->assertOk();
    }

    public function test_user_without_finance_permission_cannot_view_po(): void
    {
        $user = User::query()->where('email', 'poolstaff@blueerp.test')->firstOrFail();

        $this->actingAs($user)->get('/finance/purchase-orders')->assertForbidden();
    }

    public function test_create_po_from_confirmed_booking(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $booking = $this->makeBooking('confirmed');

        Livewire::actingAs($user)
            ->test(PurchaseOrderCreate::class)
            ->set('booking_id', $booking->id)
            ->set('subtotal', 1000000)
            ->set('tax', 110000)
            ->set('total', 1110000)
            ->set('status', 'draft')
            ->call('save');

        $this->assertDatabaseHas('purchase_orders', [
            'booking_id' => $booking->id,
            'status' => 'draft',
        ]);
    }

    public function test_cannot_create_po_from_pending_booking(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $booking = $this->makeBooking('pending');

        Livewire::actingAs($user)
            ->test(PurchaseOrderCreate::class)
            ->set('booking_id', $booking->id)
            ->set('subtotal', 1000000)
            ->set('tax', 110000)
            ->set('total', 1110000)
            ->set('status', 'draft')
            ->call('save')
            ->assertSet('errorMessage', 'PO can only be created from confirmed booking.');
    }

    public function test_po_number_auto_generated(): void
    {
        $booking = $this->makeBooking('confirmed');

        $po = app(FinanceFlowService::class)->createPurchaseOrder([
            'booking_id' => $booking->id,
            'subtotal' => 1000,
            'tax' => 100,
            'total' => 1100,
            'status' => 'draft',
        ]);

        $this->assertMatchesRegularExpression('/^PO-\d{6}-\d{4}$/', $po->po_number);
    }

    public function test_approve_po_changes_status_and_approved_by(): void
    {
        $approver = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $po = $this->makePurchaseOrder('draft');

        Livewire::actingAs($approver)
            ->test(PurchaseOrderShow::class, ['purchaseOrder' => $po])
            ->call('approve');

        $po->refresh();
        $this->assertSame('approved', $po->status);
        $this->assertSame($approver->id, $po->approved_by);
        $this->assertNotNull($po->approved_at);
    }

    public function test_create_invoice_from_approved_po(): void
    {
        $po = $this->makePurchaseOrder('approved');

        $invoice = app(FinanceFlowService::class)->createInvoiceFromPurchaseOrder($po, [
            'send_now' => true,
            'issued_at' => now()->toDateString(),
            'due_at' => now()->addDays(14)->toDateString(),
        ]);

        $this->assertSame($po->id, $invoice->purchase_order_id);
        $this->assertSame('sent', $invoice->status);
    }

    public function test_cannot_create_invoice_from_unapproved_po(): void
    {
        $po = $this->makePurchaseOrder('pending');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invoice can only be created from approved PO.');

        app(FinanceFlowService::class)->createInvoiceFromPurchaseOrder($po);
    }

    public function test_invoice_number_auto_generated(): void
    {
        $po = $this->makePurchaseOrder('approved');
        $invoice = app(FinanceFlowService::class)->createInvoiceFromPurchaseOrder($po);

        $this->assertMatchesRegularExpression('/^INV-\d{6}-\d{4}$/', $invoice->invoice_number);
    }

    public function test_creating_invoice_changes_po_to_invoiced(): void
    {
        $po = $this->makePurchaseOrder('approved');

        app(FinanceFlowService::class)->createInvoiceFromPurchaseOrder($po);

        $this->assertSame('invoiced', $po->fresh()->status);
    }

    public function test_add_partial_payment_updates_invoice_to_partial(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $invoice = $this->makeInvoice(total: 2000000, status: 'sent');

        Livewire::actingAs($user)
            ->test(InvoiceShow::class, ['invoice' => $invoice])
            ->set('paid_at', now()->toDateString())
            ->set('amount', 500000)
            ->set('method', 'bank_transfer')
            ->call('addPayment');

        $invoice->refresh();
        $this->assertSame('partial', $invoice->status);
        $this->assertEquals(500000, (float) $invoice->paid_amount);
    }

    public function test_add_full_payment_updates_invoice_to_paid(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $invoice = $this->makeInvoice(total: 900000, status: 'sent');

        Livewire::actingAs($user)
            ->test(InvoiceShow::class, ['invoice' => $invoice])
            ->set('paid_at', now()->toDateString())
            ->set('amount', 900000)
            ->set('method', 'cash')
            ->call('addPayment');

        $invoice->refresh();
        $this->assertSame('paid', $invoice->status);
    }

    public function test_cannot_overpay_invoice(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $invoice = $this->makeInvoice(total: 500000, status: 'sent');

        Livewire::actingAs($user)
            ->test(InvoiceShow::class, ['invoice' => $invoice])
            ->set('paid_at', now()->toDateString())
            ->set('amount', 600000)
            ->set('method', 'other')
            ->call('addPayment')
            ->assertSet('errorMessage', 'Payment exceeds invoice total amount.');
    }

    public function test_create_e_voucher(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();

        Livewire::actingAs($user)
            ->test(EVoucherCreate::class)
            ->set('amount', 300000)
            ->set('used_amount', 0)
            ->set('status', 'active')
            ->call('save');

        $voucher = EVoucher::query()->latest('id')->firstOrFail();

        $this->assertMatchesRegularExpression('/^EV-\d{6}-\d{4}$/', $voucher->code);
    }

    public function test_payment_using_e_voucher_updates_used_amount(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $invoice = $this->makeInvoice(total: 500000, status: 'sent');
        $voucher = EVoucher::factory()->create([
            'client_id' => $invoice->client_id,
            'status' => 'active',
            'amount' => 600000,
            'used_amount' => 100000,
            'expired_at' => now()->addDays(10)->toDateString(),
        ]);

        Livewire::actingAs($user)
            ->test(InvoiceShow::class, ['invoice' => $invoice])
            ->set('paid_at', now()->toDateString())
            ->set('amount', 200000)
            ->set('method', 'evoucher')
            ->set('e_voucher_id', $voucher->id)
            ->call('addPayment');

        $voucher->refresh();
        $this->assertEquals(300000, (float) $voucher->used_amount);
    }

    public function test_expired_e_voucher_cannot_be_used(): void
    {
        $user = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();
        $invoice = $this->makeInvoice(total: 500000, status: 'sent');
        $voucher = EVoucher::factory()->create([
            'client_id' => $invoice->client_id,
            'status' => 'active',
            'amount' => 600000,
            'used_amount' => 0,
            'expired_at' => now()->subDay()->toDateString(),
        ]);

        Livewire::actingAs($user)
            ->test(InvoiceShow::class, ['invoice' => $invoice])
            ->set('paid_at', now()->toDateString())
            ->set('amount', 200000)
            ->set('method', 'evoucher')
            ->set('e_voucher_id', $voucher->id)
            ->call('addPayment')
            ->assertSet('errorMessage', 'E-voucher already expired.');
    }

    private function makeBooking(string $status): Booking
    {
        $client = Client::factory()->create(['status' => 'active']);
        $pool = Pool::factory()->create(['status' => 'active']);
        $user = User::query()->where('email', 'sales@blueerp.test')->firstOrFail();

        return Booking::factory()->create([
            'booking_number' => 'BK-209901-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'client_id' => $client->id,
            'requested_by' => $user->id,
            'pool_id' => $pool->id,
            'status' => $status,
        ]);
    }

    private function makePurchaseOrder(string $status): PurchaseOrder
    {
        $booking = $this->makeBooking('confirmed');
        $finance = User::query()->where('email', 'finance@blueerp.test')->firstOrFail();

        return PurchaseOrder::query()->create([
            'po_number' => 'PO-209901-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
            'status' => $status,
            'subtotal' => 1000000,
            'tax' => 110000,
            'total' => 1110000,
            'approved_by' => $status === 'approved' ? $finance->id : null,
            'approved_at' => $status === 'approved' ? now() : null,
        ]);
    }

    private function makeInvoice(float $total, string $status): Invoice
    {
        $po = $this->makePurchaseOrder('approved');

        return Invoice::query()->create([
            'invoice_number' => 'INV-209901-'.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'purchase_order_id' => $po->id,
            'client_id' => $po->client_id,
            'status' => $status,
            'issued_at' => now()->toDateString(),
            'due_at' => now()->addDays(20)->toDateString(),
            'subtotal' => $total,
            'tax' => 0,
            'total' => $total,
            'paid_amount' => 0,
        ]);
    }
}
