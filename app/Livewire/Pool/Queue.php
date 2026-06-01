<?php

namespace App\Livewire\Pool;

use App\Models\Booking;
use App\Models\Pool;
use Livewire\Component;
use Livewire\WithPagination;

class Queue extends Component
{
    use WithPagination;

    public string $status = 'pending';
    public ?int $pool_id = null;
    public string $search = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('pool.view-all') || auth()->user()->can('pool.view-own'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['status', 'pool_id', 'search'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $bookings = Booking::query()
            ->with(['client', 'pool', 'vehicle', 'driver'])
            ->whereIn('status', ['pending', 'assigned'])
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->pool_id, fn ($query) => $query->where('pool_id', $this->pool_id))
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($inner): void {
                    $inner->where('booking_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('client', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->orderBy('start_datetime')
            ->paginate(10);

        return view('livewire.pool.queue', [
            'bookings' => $bookings,
            'pools' => Pool::query()->orderBy('name')->get(),
        ]);
    }
}
