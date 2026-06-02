<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use App\Services\MaintenanceService;
use Livewire\Component;

class Edit extends Component
{
    public MaintenanceLog $maintenanceLog;

    public ?int $vehicle_id = null;
    public string $title = '';
    public string $status = 'scheduled';
    public ?string $start_at = null;
    public ?string $end_at = null;
    public float $cost = 0;
    public ?string $notes = null;

    public function mount(MaintenanceLog $maintenanceLog): void
    {
        abort_unless(auth()->user()->can('maintenance.update'), 403);

        $this->maintenanceLog = $maintenanceLog->load('vehicle');
        $this->vehicle_id = $maintenanceLog->vehicle_id;
        $this->title = $maintenanceLog->title;
        $this->status = $maintenanceLog->status;
        $this->start_at = $maintenanceLog->start_at?->format('Y-m-d\TH:i');
        $this->end_at = $maintenanceLog->end_at?->format('Y-m-d\TH:i');
        $this->cost = (float) $maintenanceLog->cost;
        $this->notes = $maintenanceLog->notes;
    }

    protected function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,in_progress,completed,cancelled'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'cost' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function save(MaintenanceService $service)
    {
        $service->update($this->maintenanceLog, $this->validate());

        session()->flash('success', 'Maintenance log updated successfully.');

        return redirect()->route('maintenance.show', $this->maintenanceLog);
    }

    public function render()
    {
        return view('livewire.maintenance.edit', [
            'vehicles' => Vehicle::query()->orderBy('plate_number')->get(),
            'breadcrumbs' => [
                ['label' => 'Maintenance', 'url' => route('maintenance.index')],
                ['label' => $this->maintenanceLog->title, 'url' => route('maintenance.show', $this->maintenanceLog)],
                ['label' => 'Edit', 'url' => route('maintenance.edit', $this->maintenanceLog)],
            ],
        ]);
    }
}
