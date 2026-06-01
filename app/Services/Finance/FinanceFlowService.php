<?php

namespace App\Services\Finance;

use App\Models\Booking;
use App\Models\EVoucher;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FinanceFlowService
{
    public function __construct(private readonly DocumentNumberService $numbers)
    {
    }

    public function createPurchaseOrder(array $payload): PurchaseOrder
    {
        $booking = Booking::query()->with('client')->findOrFail($payload['booking_id']);

        if ($booking->status !== 'confirmed') {
            throw new RuntimeException('PO can only be created from confirmed booking.');
        }

        return PurchaseOrder::query()->create([
            'po_number' => $this->numbers->next(PurchaseOrder::class, 'po_number', 'PO'),
            'booking_id' => $booking->id,
            'client_id' => $booking->client_id,
            'status' => $payload['status'] ?? 'draft',
            'subtotal' => $payload['subtotal'],
            'tax' => $payload['tax'],
            'total' => $payload['total'],
        ]);
    }

    public function approvePurchaseOrder(PurchaseOrder $purchaseOrder, int $approvedBy): void
    {
        if (! in_array($purchaseOrder->status, ['draft', 'pending'], true)) {
            throw new RuntimeException('Only draft or pending PO can be approved.');
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function createInvoiceFromPurchaseOrder(PurchaseOrder $purchaseOrder, array $payload = []): Invoice
    {
        if ($purchaseOrder->status !== 'approved') {
            throw new RuntimeException('Invoice can only be created from approved PO.');
        }

        $activeExists = Invoice::query()
            ->where('purchase_order_id', $purchaseOrder->id)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($activeExists) {
            throw new RuntimeException('PO already has an active invoice.');
        }

        return DB::transaction(function () use ($purchaseOrder, $payload): Invoice {
            $invoice = Invoice::query()->create([
                'invoice_number' => $this->numbers->next(Invoice::class, 'invoice_number', 'INV'),
                'purchase_order_id' => $purchaseOrder->id,
                'client_id' => $purchaseOrder->client_id,
                'status' => ($payload['send_now'] ?? false) ? 'sent' : 'draft',
                'issued_at' => $payload['issued_at'] ?? now()->toDateString(),
                'due_at' => $payload['due_at'] ?? now()->addDays(30)->toDateString(),
                'subtotal' => $purchaseOrder->subtotal,
                'tax' => $purchaseOrder->tax,
                'total' => $purchaseOrder->total,
                'paid_amount' => 0,
            ]);

            $purchaseOrder->update(['status' => 'invoiced']);

            return $invoice;
        });
    }

    public function addPayment(Invoice $invoice, array $payload, int $userId): Payment
    {
        $amount = (float) $payload['amount'];

        if ($amount <= 0) {
            throw new RuntimeException('Payment amount must be greater than zero.');
        }

        return DB::transaction(function () use ($invoice, $payload, $amount, $userId): Payment {
            $invoice->refresh();

            $newTotalPaid = (float) $invoice->paid_amount + $amount;
            if ($newTotalPaid > (float) $invoice->total) {
                throw new RuntimeException('Payment exceeds invoice total amount.');
            }

            $voucherId = null;

            if (($payload['method'] ?? null) === 'evoucher') {
                $voucher = EVoucher::query()->findOrFail($payload['e_voucher_id'] ?? 0);

                if ($voucher->status !== 'active') {
                    throw new RuntimeException('E-voucher is not active.');
                }

                if ($voucher->expired_at && now()->startOfDay()->gt($voucher->expired_at->startOfDay())) {
                    throw new RuntimeException('E-voucher already expired.');
                }

                $remaining = (float) $voucher->amount - (float) $voucher->used_amount;
                if ($remaining < $amount) {
                    throw new RuntimeException('E-voucher remaining amount is not enough.');
                }

                $voucher->used_amount = (float) $voucher->used_amount + $amount;
                if ((float) $voucher->used_amount >= (float) $voucher->amount) {
                    $voucher->status = 'used';
                    $voucher->used_at = now();
                }
                $voucher->save();

                $voucherId = $voucher->id;
            }

            $payment = Payment::query()->create([
                'invoice_id' => $invoice->id,
                'payment_number' => $this->numbers->next(Payment::class, 'payment_number', 'PAY'),
                'paid_at' => $payload['paid_at'],
                'amount' => $amount,
                'method' => $payload['method'],
                'reference_number' => $payload['reference_number'] ?? null,
                'notes' => $payload['notes'] ?? null,
                'created_by' => $userId,
                'e_voucher_id' => $voucherId,
            ]);

            $invoice->paid_amount = $newTotalPaid;

            if ((float) $invoice->paid_amount >= (float) $invoice->total) {
                $invoice->status = 'paid';
            } elseif ((float) $invoice->paid_amount > 0) {
                $invoice->status = 'partial';
            } elseif ($invoice->status === 'draft') {
                $invoice->status = 'sent';
            }

            if ($invoice->due_at && now()->startOfDay()->gt($invoice->due_at->startOfDay()) && in_array($invoice->status, ['sent', 'partial'], true)) {
                $invoice->status = 'overdue';
            }

            $invoice->save();

            return $payment;
        });
    }
}
