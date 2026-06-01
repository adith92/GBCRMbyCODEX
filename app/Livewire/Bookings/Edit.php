<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Pool;
use Livewire\Component;

class Edit extends Component
{
    public Booking $booking;

    public ?int $client_id = null;
    public ?int $pool_id = null;
    public string $start_datetime = '';
    public string $end_datetime = '';
    public ?string $pickup_location = null;
    public ?string $destination = null;
    public ?string $notes = null;

    public function mount(Booking $booking): void
    {
        abort_unless(auth()->user()->can('bookings.update'), 403);

        if (! in_array($booking->status, ['pending', 'assigned'], true) && ! auth()->user()->hasRole('super-admin')) {
            abort(403, 'Booking with current status cannot be edited.');
        }

        $this->booking = $booking;
        $this->client_id = $booking->client_id;
        $this->pool_id = $booking->pool_id;
        $this->start_datetime = optional($booking->start_datetime)->format('Y-m-d\TH:i');
        $this->end_datetime = optional($booking->end_datetime)->format('Y-m-d\TH:i');
        $this->pickup_location = $booking->pickup_location;
        $this->destination = $booking->destination;
        $this->notes = $booking->notes;
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

    public function save()
    {
        if (! in_array($this->booking->status, ['pending', 'assigned'], true) && ! auth()->user()->hasRole('super-admin')) {
            abort(403, 'Booking with current status cannot be edited.');
        }

        $this->booking->update($this->validate());

        session()->flash('success', 'Booking updated successfully.');

        return redirect()->route('bookings.show', $this->booking);
    }

    public function render()
    {
        return view('livewire.bookings.edit', [
            'clients' => Client::query()->orderBy('name')->get(),
            'pools' => Pool::query()->orderBy('name')->get(),
        ]);
    }
}
