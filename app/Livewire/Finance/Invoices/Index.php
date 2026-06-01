<?php

namespace App\Livewire\Finance\Invoices;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('invoices.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $invoices = Invoice::query()
            ->with(['client', 'purchaseOrder'])
            ->when($this->search !== '', fn ($q) => $q->where('invoice_number', 'like', '%'.$this->search.'%'))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);

        return view('livewire.finance.invoices.index', compact('invoices'));
    }
}
