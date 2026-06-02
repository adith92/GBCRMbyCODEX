<?php

namespace App\Livewire\Maintenance;

use App\Models\Vehicle;
use App\Services\MaintenanceService;
use Livewire\Component;

class Create extends Component
{
    public ?int $vehicle_id = null;
    public string $title = '';
    public string $status = 'scheduled';
    public ?string $start_at = null;
    public ?string $end_at = null;
    public float $cost = 0;
    public ?string $notes = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('maintenance.create'), 403);
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
        $log = $service->create($this->validate(), (int) auth()->id());

        session()->flash('success', 'Maintenance log created successfully.');

        return redirect()->route('maintenance.show', $log);
    }

    public function render()
    {
        return view('livewire.maintenance.create', [
            'vehicles' => Vehicle::query()->orderBy('plate_number')->get(),
            'breadcrumbs' => [
                ['label' => 'Maintenance', 'url' => route('maintenance.index')],
                ['label' => 'Create', 'url' => route('maintenance.create')],
            ],
        ]);
    }
}
