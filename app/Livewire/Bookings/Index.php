<?php

namespace App\Livewire\Bookings;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $startDateFrom = '';
    public string $startDateTo = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->can('bookings.view'), 403);
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'startDateFrom', 'startDateTo'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $bookings = Booking::query()
            ->with(['client', 'pool'])
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($inner): void {
                    $inner->where('booking_number', 'like', '%'.$this->search.'%')
                        ->orWhereHas('client', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->startDateFrom !== '', fn ($query) => $query->whereDate('start_datetime', '>=', $this->startDateFrom))
            ->when($this->startDateTo !== '', fn ($query) => $query->whereDate('start_datetime', '<=', $this->startDateTo))
            ->latest()
            ->paginate(10);

        return view('livewire.bookings.index', [
            'bookings' => $bookings,
        ]);
    }
}
