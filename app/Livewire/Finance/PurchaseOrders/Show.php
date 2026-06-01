<?php

namespace App\Livewire\Finance\PurchaseOrders;

use App\Models\PurchaseOrder;
use App\Services\Finance\FinanceFlowService;
use Livewire\Component;
use RuntimeException;

class Show extends Component
{
    public PurchaseOrder $purchaseOrder;

    public string $invoice_action = 'draft';
    public ?string $due_at = null;
    public ?string $errorMessage = null;

    public function mount(PurchaseOrder $purchaseOrder): void
    {
        abort_unless(auth()->user()->can('purchase-orders.view'), 403);
        $this->purchaseOrder = $purchaseOrder->load(['booking', 'client', 'approvedBy', 'invoices']);
        $this->due_at = now()->addDays(30)->toDateString();
    }

    public function approve(FinanceFlowService $service): void
    {
        abort_unless(auth()->user()->can('purchase-orders.approve'), 403);
        $this->errorMessage = null;

        try {
            $service->approvePurchaseOrder($this->purchaseOrder, (int) auth()->id());
            $this->purchaseOrder->refresh();
            session()->flash('success', 'Purchase Order approved.');
        } catch (RuntimeException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function createInvoice(FinanceFlowService $service)
    {
        abort_unless(auth()->user()->can('invoices.create'), 403);
        $this->errorMessage = null;

        try {
            $invoice = $service->createInvoiceFromPurchaseOrder($this->purchaseOrder, [
                'send_now' => $this->invoice_action === 'sent',
                'issued_at' => now()->toDateString(),
                'due_at' => $this->due_at,
            ]);

            session()->flash('success', 'Invoice generated from PO.');

            return redirect()->route('finance.invoices.show', $invoice);
        } catch (RuntimeException $exception) {
            $this->errorMessage = $exception->getMessage();
        }

        return null;
    }

    public function render()
    {
        return view('livewire.finance.purchase-orders.show');
    }
}
