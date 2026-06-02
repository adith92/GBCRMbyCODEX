<?php

namespace App\Services;

use App\Models\MaintenanceLog;
use App\Models\Vehicle;

class MaintenanceService
{
    public function syncVehicleStatus(MaintenanceLog $maintenanceLog): void
    {
        $vehicle = $maintenanceLog->vehicle()->first();

        if (! $vehicle) {
            return;
        }

        if ($maintenanceLog->status === 'in_progress') {
            $vehicle->update(['status' => 'maintenance']);

            return;
        }

        if (in_array($maintenanceLog->status, ['completed', 'cancelled'], true)
            && ! $vehicle->hasActiveMaintenance()
            && ! $vehicle->hasActiveBooking()
            && $vehicle->status === 'maintenance') {
            $vehicle->update(['status' => 'available']);
        }
    }

    public function create(array $validated, int $reportedBy): MaintenanceLog
    {
        $log = MaintenanceLog::query()->create([
            ...$validated,
            'reported_by' => $reportedBy,
        ]);

        $log->load('vehicle');
        $this->syncVehicleStatus($log);

        return $log;
    }

    public function update(MaintenanceLog $maintenanceLog, array $validated): void
    {
        $maintenanceLog->update($validated);
        $maintenanceLog->refresh()->load('vehicle');
        $this->syncVehicleStatus($maintenanceLog);
    }
}
