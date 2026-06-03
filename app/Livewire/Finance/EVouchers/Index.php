<?php

namespace App\Livewire\Finance\EVouchers;

use App\Models\EVoucher;
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

    public function mount(): void
    {
        abort_unless(auth()->user()->can('evouchers.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'sortBy', 'sortDirection'], true)) {
            $this->resetPage();
        }
    }

    public function sort(string $field): void
    {
        $allowed = ['code', 'client_name', 'status', 'amount', 'created_at'];
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
        $items = EVoucher::query()
            ->with('client')
            ->when($this->search !== '', fn ($q) => $q->where('code', 'like', '%'.$this->search.'%'))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->when($this->sortBy === 'client_name', fn ($query) => $query->leftJoin('clients', 'clients.id', '=', 'e_vouchers.client_id')->orderBy('clients.name', $this->sortDirection)->select('e_vouchers.*'))
            ->when(in_array($this->sortBy, ['code', 'status', 'amount', 'created_at'], true), fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);

        return view('livewire.finance.e-vouchers.index', [
            'vouchers' => $items,
        ]);
    }
}
