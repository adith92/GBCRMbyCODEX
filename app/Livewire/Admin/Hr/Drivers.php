<?php

namespace App\Livewire\Admin\Hr;

use App\Models\Driver;
use Livewire\Component;
use Livewire\WithPagination;

class Drivers extends Component
{
    use WithPagination;

    public string $search = '';

    public function mount(): void
    {
        abort_unless(
            auth()->user()->hasRole('super-admin')
            && auth()->user()->can('admin.access')
            && auth()->user()->can('hr.view')
            && auth()->user()->can('hr.drivers.manage'),
            403
        );
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.hr.drivers', [
            'drivers' => Driver::query()
                ->with('pool')
                ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
                ->latest()
                ->paginate(10),
            'breadcrumbs' => [
                ['label' => 'HR Drivers', 'url' => route('admin.hr.drivers')],
            ],
        ]);
    }
}
