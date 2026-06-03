<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $status = '';
    public ?int $vehicle_id = null;
    #[Url(as: 'search')]
    public string $search = '';
    #[Url(as: 'sort')]
    public string $sortBy = 'created_at';
    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('maintenance.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['status', 'vehicle_id', 'search', 'sortBy', 'sortDirection'], true)) {
            $this->resetPage();
        }
    }

    public function sort(string $field): void
    {
        $allowed = ['title', 'vehicle_name', 'status', 'cost', 'created_at'];
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
        return view('livewire.maintenance.index', [
            'maintenanceLogs' => MaintenanceLog::query()
                ->with(['vehicle.pool', 'reportedBy'])
                ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
                ->when($this->vehicle_id, fn ($q) => $q->where('vehicle_id', $this->vehicle_id))
                ->when($this->search !== '', function ($query): void {
                    $query->where('title', 'like', '%'.$this->search.'%')
                        ->orWhereHas('vehicle', fn ($vehicle) => $vehicle->where('plate_number', 'like', '%'.$this->search.'%'));
                })
                ->when($this->sortBy === 'vehicle_name', fn ($query) => $query->leftJoin('vehicles', 'vehicles.id', '=', 'maintenance_logs.vehicle_id')->orderBy('vehicles.plate_number', $this->sortDirection)->select('maintenance_logs.*'))
                ->when(in_array($this->sortBy, ['title', 'status', 'cost', 'created_at'], true), fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
                ->paginate(10),
            'vehicles' => Vehicle::query()->orderBy('plate_number')->get(),
            'breadcrumbs' => [
                ['label' => 'Maintenance', 'url' => route('maintenance.index')],
            ],
        ]);
    }
}
