<?php

namespace App\Livewire\Maintenance;

use App\Models\MaintenanceLog;
use Livewire\Component;

class Show extends Component
{
    public MaintenanceLog $maintenanceLog;

    public function mount(MaintenanceLog $maintenanceLog): void
    {
        abort_unless(auth()->user()->can('maintenance.view'), 403);

        $this->maintenanceLog = $maintenanceLog->load(['vehicle.pool', 'reportedBy']);
    }

    public function render()
    {
        return view('livewire.maintenance.show', [
            'breadcrumbs' => [
                ['label' => 'Maintenance', 'url' => route('maintenance.index')],
                ['label' => $this->maintenanceLog->title, 'url' => route('maintenance.show', $this->maintenanceLog)],
            ],
        ]);
    }
}
