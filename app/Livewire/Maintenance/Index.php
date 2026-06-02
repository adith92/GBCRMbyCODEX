<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $status = '';
    public ?int $vehicle_id = null;
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('maintenance.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['status', 'vehicle_id', 'search'], true)) {
            $this->resetPage();
        }
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
                ->latest()
                ->paginate(10),
            'vehicles' => Vehicle::query()->orderBy('plate_number')->get(),
            'breadcrumbs' => [
                ['label' => 'Maintenance', 'url' => route('maintenance.index')],
            ],
        ]);
    }
}
