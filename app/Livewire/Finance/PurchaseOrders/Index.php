<?php

namespace App\Livewire\Finance\PurchaseOrders;

use App\Models\Client;
use App\Models\PurchaseOrder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';
    #[Url(as: 'status')]
    public string $status = '';
    #[Url(as: 'sort')]
    public string $sortBy = 'created_at';
    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';
    public ?int $client_id = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('purchase-orders.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'client_id', 'sortBy', 'sortDirection'], true)) {
            $this->resetPage();
        }
    }

    public function sort(string $field): void
    {
        $allowed = ['po_number', 'client_name', 'status', 'total', 'created_at'];
        if (! in_array($field, $allowed, true)) {
            return;
        }
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $items = PurchaseOrder::query()
            ->with(['client', 'booking', 'approvedBy'])
            ->when($this->search !== '', fn ($q) => $q->where('po_number', 'like', '%'.$this->search.'%'))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->client_id, fn ($q) => $q->where('client_id', $this->client_id))
            ->when($this->sortBy === 'client_name', fn ($query) => $query->leftJoin('clients', 'clients.id', '=', 'purchase_orders.client_id')->orderBy('clients.name', $this->sortDirection)->select('purchase_orders.*'))
            ->when(in_array($this->sortBy, ['po_number', 'status', 'total', 'created_at'], true), fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);

        return view('livewire.finance.purchase-orders.index', [
            'purchaseOrders' => $items,
            'clients' => Client::query()->orderBy('name')->get(),
        ]);
    }
}
