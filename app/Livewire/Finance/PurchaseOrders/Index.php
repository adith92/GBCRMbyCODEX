<?php

namespace App\Livewire\Finance\PurchaseOrders;

use App\Models\Client;
use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public ?int $client_id = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('purchase-orders.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'client_id'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $items = PurchaseOrder::query()
            ->with(['client', 'booking', 'approvedBy'])
            ->when($this->search !== '', fn ($q) => $q->where('po_number', 'like', '%'.$this->search.'%'))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->client_id, fn ($q) => $q->where('client_id', $this->client_id))
            ->latest()
            ->paginate(10);

        return view('livewire.finance.purchase-orders.index', [
            'purchaseOrders' => $items,
            'clients' => Client::query()->orderBy('name')->get(),
        ]);
    }
}
