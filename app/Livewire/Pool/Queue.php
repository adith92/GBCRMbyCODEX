<?php

namespace App\Livewire\Pool;

use App\Models\Booking;
use App\Models\Pool;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Queue extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $status = 'pending';

    #[Url(as: 'pool')]
    public ?int $pool_id = null;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'start_datetime';

    #[Url(as: 'dir')]
    public string $sortDirection = 'asc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('pool.view-all') || auth()->user()->can('pool.view-own'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['status', 'pool_id', 'search', 'sortBy', 'sortDirection'], true)) {
            $this->resetPage();
        }
    }

    public function sort(string $field): void
    {
        $allowed = ['booking_number', 'client_name', 'pool_name', 'start_datetime', 'status'];

        if (! in_array($field, $allowed, true)) {
            return;
        }

        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
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
            ->when($this->sortBy === 'client_name', fn ($query) => $query->join('clients', 'clients.id', '=', 'bookings.client_id')->orderBy('clients.name', $this->sortDirection)->select('bookings.*'))
            ->when($this->sortBy === 'pool_name', fn ($query) => $query->join('pools', 'pools.id', '=', 'bookings.pool_id')->orderBy('pools.name', $this->sortDirection)->select('bookings.*'))
            ->when(in_array($this->sortBy, ['booking_number', 'start_datetime', 'status'], true), fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);

        return view('livewire.pool.queue', [
            'bookings' => $bookings,
            'pools' => Pool::query()->orderBy('name')->get(),
        ]);
    }
}
