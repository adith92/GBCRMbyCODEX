<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'start_from')]
    public string $startDateFrom = '';

    #[Url(as: 'start_to')]
    public string $startDateTo = '';

    #[Url(as: 'pool')]
    public string $poolId = '';

    #[Url(as: 'sort')]
    public string $sortBy = 'start_datetime';

    #[Url(as: 'dir')]
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('bookings.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'startDateFrom', 'startDateTo', 'poolId', 'sortBy', 'sortDirection'], true)) {
            $this->resetPage();
        }
    }

    public function sort(string $field): void
    {
        $allowed = ['booking_number', 'start_datetime', 'status', 'client_name', 'pool_name', 'vehicle_name', 'driver_name'];

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
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($inner): void {
                    $inner->where('booking_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('client', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->poolId !== '', fn ($query) => $query->where('pool_id', $this->poolId))
            ->when($this->startDateFrom !== '', fn ($query) => $query->whereDate('start_datetime', '>=', $this->startDateFrom))
            ->when($this->startDateTo !== '', fn ($query) => $query->whereDate('start_datetime', '<=', $this->startDateTo))
            ->when($this->sortBy === 'client_name', fn ($query) => $query->join('clients', 'clients.id', '=', 'bookings.client_id')->orderBy('clients.name', $this->sortDirection)->select('bookings.*'))
            ->when($this->sortBy === 'pool_name', fn ($query) => $query->join('pools', 'pools.id', '=', 'bookings.pool_id')->orderBy('pools.name', $this->sortDirection)->select('bookings.*'))
            ->when($this->sortBy === 'vehicle_name', fn ($query) => $query->leftJoin('vehicles', 'vehicles.id', '=', 'bookings.vehicle_id')->orderBy('vehicles.plate_number', $this->sortDirection)->select('bookings.*'))
            ->when($this->sortBy === 'driver_name', fn ($query) => $query->leftJoin('drivers', 'drivers.id', '=', 'bookings.driver_id')->orderBy('drivers.name', $this->sortDirection)->select('bookings.*'))
            ->when(in_array($this->sortBy, ['booking_number', 'start_datetime', 'status'], true), fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);

        return view('livewire.bookings.index', [
            'bookings' => $bookings,
        ]);
    }
}
