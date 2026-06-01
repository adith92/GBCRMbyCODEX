<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\DriverAssignment;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingDispatchService
{
    public function assign(Booking $booking, int $vehicleId, int $driverId, int $assignedBy): void
    {
        if (! in_array($booking->status, ['pending', 'assigned'], true)) {
            throw new RuntimeException('Booking status does not allow assignment.');
        }

        $vehicle = Vehicle::query()->findOrFail($vehicleId);

        if ($vehicle->status !== 'available') {
            throw new RuntimeException('Selected vehicle is not available.');
        }

        if ($this->hasVehicleOverlap($booking, $vehicleId)) {
            throw new RuntimeException('Vehicle has overlapping active booking.');
        }

        if ($this->hasDriverOverlap($booking, $driverId)) {
            throw new RuntimeException('Driver has overlapping active booking.');
        }

        DB::transaction(function () use ($booking, $vehicle, $driverId, $assignedBy): void {
            $previousVehicleId = $booking->vehicle_id;

            $booking->forceFill([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driverId,
                'pool_id' => $booking->pool_id ?: $vehicle->pool_id,
                'status' => 'assigned',
            ])->save();

            DriverAssignment::query()->create([
                'booking_id' => $booking->id,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driverId,
                'assignment_type' => 'primary',
                'assigned_by' => $assignedBy,
                'assigned_at' => now(),
            ]);

            if ($vehicle->status !== 'maintenance' && $vehicle->status !== 'hold') {
                $vehicle->update(['status' => 'po']);
            }

            if ($previousVehicleId && (int) $previousVehicleId !== (int) $vehicle->id) {
                $this->releaseVehicleIfNoActiveBooking((int) $previousVehicleId, (int) $booking->id);
            }
        });
    }

    public function confirm(Booking $booking): void
    {
        if ($booking->status !== 'assigned') {
            throw new RuntimeException('Only assigned booking can be confirmed.');
        }

        if (! $booking->vehicle_id || ! $booking->driver_id) {
            throw new RuntimeException('Booking requires assigned vehicle and driver before confirmation.');
        }

        $booking->update(['status' => 'confirmed']);
    }

    public function cancel(Booking $booking): void
    {
        if (! in_array($booking->status, ['pending', 'assigned', 'confirmed'], true)) {
            throw new RuntimeException('Booking cannot be cancelled from current status.');
        }

        DB::transaction(function () use ($booking): void {
            DriverAssignment::query()
                ->where('booking_id', $booking->id)
                ->whereNull('released_at')
                ->update(['released_at' => now()]);

            $vehicleId = $booking->vehicle_id;

            $booking->update(['status' => 'cancelled']);

            if ($vehicleId) {
                $this->releaseVehicleIfNoActiveBooking((int) $vehicleId, $booking->id);
            }
        });
    }

    public function hasVehicleOverlap(Booking $booking, int $vehicleId): bool
    {
        return Booking::query()
            ->where('id', '!=', $booking->id)
            ->where('vehicle_id', $vehicleId)
            ->whereIn('status', ['assigned', 'confirmed'])
            ->where(function ($query) use ($booking): void {
                $query->where('start_datetime', '<', $booking->end_datetime)
                    ->where('end_datetime', '>', $booking->start_datetime);
            })
            ->exists();
    }

    public function hasDriverOverlap(Booking $booking, int $driverId): bool
    {
        return Booking::query()
            ->where('id', '!=', $booking->id)
            ->where('driver_id', $driverId)
            ->whereIn('status', ['assigned', 'confirmed'])
            ->where(function ($query) use ($booking): void {
                $query->where('start_datetime', '<', $booking->end_datetime)
                    ->where('end_datetime', '>', $booking->start_datetime);
            })
            ->exists();
    }

    public function releaseVehicleIfNoActiveBooking(int $vehicleId, ?int $ignoreBookingId = null): void
    {
        $hasActive = Booking::query()
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('vehicle_id', $vehicleId)
            ->whereIn('status', ['assigned', 'confirmed'])
            ->exists();

        if (! $hasActive) {
            $vehicle = Vehicle::query()->find($vehicleId);
            if ($vehicle && in_array($vehicle->status, ['po', 'available'], true)) {
                $vehicle->update(['status' => 'available']);
            }
        }
    }
}
