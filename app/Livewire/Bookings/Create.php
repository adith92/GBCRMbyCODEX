<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Pool;
use App\Services\BookingNumberService;
use Livewire\Component;

class Create extends Component
{
    public ?int $client_id = null;
    public ?int $pool_id = null;
    public string $start_datetime = '';
    public string $end_datetime = '';
    public ?string $pickup_location = null;
    public ?string $destination = null;
    public ?string $notes = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('bookings.create'), 403);
    }

    protected function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'pool_id' => ['nullable', 'exists:pools,id'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
            'pickup_location' => ['nullable', 'string', 'max:1000'],
            'destination' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function save(BookingNumberService $numberService)
    {
        $validated = $this->validate();

        $booking = Booking::query()->create([
            ...$validated,
            'booking_number' => $numberService->generate(),
            'requested_by' => auth()->id(),
            'status' => 'pending',
        ]);

        session()->flash('success', 'Booking created successfully.');

        return redirect()->route('bookings.show', $booking);
    }

    public function render()
    {
        return view('livewire.bookings.create', [
            'clients' => Client::query()->orderBy('name')->get(),
            'pools' => Pool::query()->orderBy('name')->get(),
        ]);
    }
}
