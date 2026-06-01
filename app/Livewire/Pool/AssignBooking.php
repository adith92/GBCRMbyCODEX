<?php

namespace App\Livewire\Pool;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Services\BookingDispatchService;
use Livewire\Component;
use RuntimeException;

class AssignBooking extends Component
{
    public Booking $booking;

    public ?int $vehicle_id = null;
    public ?int $driver_id = null;
    public ?string $errorMessage = null;

    public function mount(Booking $booking): void
    {
        abort_unless(auth()->user()->can('pool.assign-driver'), 403);

        if (! in_array($booking->status, ['pending', 'assigned'], true)) {
            abort(403, 'Booking is not assignable.');
        }

        $this->booking = $booking->load(['client', 'pool', 'vehicle', 'driver']);
        $this->vehicle_id = $booking->vehicle_id;
        $this->driver_id = $booking->driver_id;
    }

    protected function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
        ];
    }

    public function save(BookingDispatchService $dispatchService)
    {
        $this->validate();
        $this->errorMessage = null;

        try {
            $dispatchService->assign($this->booking, (int) $this->vehicle_id, (int) $this->driver_id, (int) auth()->id());
            session()->flash('success', 'Booking dispatched successfully.');

            return redirect()->route('bookings.show', $this->booking->fresh());
        } catch (RuntimeException $exception) {
            $message = $exception->getMessage();

            if (str_contains(strtolower($message), 'vehicle')) {
                $this->addError('vehicle_id', $message);
            } elseif (str_contains(strtolower($message), 'driver')) {
                $this->addError('driver_id', $message);
            } else {
                $this->errorMessage = $message;
            }
        }

        return null;
    }

    public function render()
    {
        $poolId = $this->booking->pool_id;

        return view('livewire.pool.assign-booking', [
            'vehicles' => Vehicle::query()
                ->where('status', 'available')
                ->when($poolId, fn ($query) => $query->where('pool_id', $poolId))
                ->orderBy('plate_number')
                ->get(),
            'drivers' => Driver::query()
                ->where('status', 'active')
                ->when($poolId, fn ($query) => $query->where('pool_id', $poolId))
                ->orderBy('name')
                ->get(),
        ]);
    }
}
