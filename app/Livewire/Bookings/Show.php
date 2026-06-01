<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Services\BookingDispatchService;
use Livewire\Component;
use RuntimeException;

class Show extends Component
{
    public Booking $booking;
    public ?string $errorMessage = null;

    public function mount(Booking $booking): void
    {
        abort_unless(auth()->user()->can('bookings.view'), 403);
        $this->booking = $booking->load(['client', 'pool', 'vehicle', 'driver', 'requestedBy', 'driverAssignments.driver', 'driverAssignments.vehicle', 'driverAssignments.assignedBy']);
    }

    public function confirm(BookingDispatchService $dispatchService): void
    {
        abort_unless(auth()->user()->can('bookings.approve'), 403);
        $this->errorMessage = null;

        try {
            $dispatchService->confirm($this->booking);
            $this->booking->refresh();
            session()->flash('success', 'Booking confirmed.');
        } catch (RuntimeException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function cancel(BookingDispatchService $dispatchService): void
    {
        abort_unless(auth()->user()->can('bookings.cancel'), 403);
        $this->errorMessage = null;

        try {
            $dispatchService->cancel($this->booking);
            $this->booking->refresh();
            session()->flash('success', 'Booking cancelled.');
        } catch (RuntimeException $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.bookings.show');
    }
}
