<?php

namespace App\Livewire\Finance\Invoices;

use App\Models\Invoice;
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
    public string $sortBy = 'issued_at';

    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('invoices.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'sortBy', 'sortDirection'], true)) {
            $this->resetPage();
        }
    }

    public function sort(string $field): void
    {
        $allowed = ['invoice_number', 'client_name', 'status', 'total', 'issued_at'];

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
        $invoices = Invoice::query()
            ->with(['client', 'purchaseOrder'])
            ->when($this->search !== '', fn ($q) => $q->where('invoice_number', 'like', '%'.$this->search.'%'))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->sortBy === 'client_name', fn ($query) => $query->leftJoin('clients', 'clients.id', '=', 'invoices.client_id')->orderBy('clients.name', $this->sortDirection)->select('invoices.*'))
            ->when(in_array($this->sortBy, ['invoice_number', 'status', 'total', 'issued_at'], true), fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);

        return view('livewire.finance.invoices.index', compact('invoices'));
    }
}
