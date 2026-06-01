<?php

namespace App\Livewire\Finance\PurchaseOrders;

use App\Models\Booking;
use App\Services\Finance\FinanceFlowService;
use Livewire\Component;
use RuntimeException;

class Create extends Component
{
    public ?int $booking_id = null;
    public float $subtotal = 0;
    public float $tax = 0;
    public float $total = 0;
    public string $status = 'draft';
    public ?string $errorMessage = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('purchase-orders.create'), 403);
    }

    protected function rules(): array
    {
        return [
            'booking_id' => ['required', 'exists:bookings,id'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,pending'],
        ];
    }

    public function updatedSubtotal(): void
    {
        $this->recalcTotal();
    }

    public function updatedTax(): void
    {
        $this->recalcTotal();
    }

    public function recalcTotal(): void
    {
        $this->total = round((float) $this->subtotal + (float) $this->tax, 2);
    }

    public function save(FinanceFlowService $service)
    {
        $this->errorMessage = null;

        try {
            $po = $service->createPurchaseOrder($this->validate());
            session()->flash('success', 'Purchase Order created successfully.');

            return redirect()->route('finance.purchase-orders.show', $po);
        } catch (RuntimeException $exception) {
            $this->errorMessage = $exception->getMessage();
        }

        return null;
    }

    public function render()
    {
        return view('livewire.finance.purchase-orders.create', [
            'bookings' => Booking::query()->with('client')->where('status', 'confirmed')->latest()->get(),
        ]);
    }
}
