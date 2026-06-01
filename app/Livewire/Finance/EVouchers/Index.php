<?php

namespace App\Livewire\Finance\EVouchers;

use App\Models\EVoucher;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('evouchers.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $items = EVoucher::query()
            ->with('client')
            ->when($this->search !== '', fn ($q) => $q->where('code', 'like', '%'.$this->search.'%'))
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(10);

        return view('livewire.finance.e-vouchers.index', [
            'vouchers' => $items,
        ]);
    }
}
