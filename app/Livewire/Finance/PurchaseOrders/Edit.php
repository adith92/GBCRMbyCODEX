<?php

namespace App\Livewire\Finance\PurchaseOrders;

use App\Models\PurchaseOrder;
use Livewire\Component;

class Edit extends Component
{
    public PurchaseOrder $purchaseOrder;

    public float $subtotal = 0;
    public float $tax = 0;
    public float $total = 0;
    public string $status = 'draft';

    public function mount(PurchaseOrder $purchaseOrder): void
    {
        abort_unless(auth()->user()->can('purchase-orders.create'), 403);

        if (! in_array($purchaseOrder->status, ['draft', 'pending'], true)) {
            abort(403, 'Only draft or pending PO can be edited.');
        }

        $this->purchaseOrder = $purchaseOrder;
        $this->subtotal = (float) $purchaseOrder->subtotal;
        $this->tax = (float) $purchaseOrder->tax;
        $this->total = (float) $purchaseOrder->total;
        $this->status = $purchaseOrder->status;
    }

    protected function rules(): array
    {
        return [
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,pending,cancelled'],
        ];
    }

    public function updatedSubtotal(): void
    {
        $this->total = round((float) $this->subtotal + (float) $this->tax, 2);
    }

    public function updatedTax(): void
    {
        $this->total = round((float) $this->subtotal + (float) $this->tax, 2);
    }

    public function save()
    {
        $this->purchaseOrder->update($this->validate());
        session()->flash('success', 'Purchase Order updated successfully.');

        return redirect()->route('finance.purchase-orders.show', $this->purchaseOrder);
    }

    public function render()
    {
        return view('livewire.finance.purchase-orders.edit');
    }
}
