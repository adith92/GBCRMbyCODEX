<x-layouts.app :title="'Bookings'" :header="'Bookings'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Bookings', 'url' => route('bookings.index')],
    ]" />

    <x-ui.page-header title="Booking pipeline" eyebrow="Bookings" description="Monitor incoming requests, filter operational status, and jump quickly into dispatch or finance drill-down.">
        <x-slot:actions>
            @can('bookings.create')
                <x-ui.action-button :href="route('bookings.create')" variant="primary">+ New Booking</x-ui.action-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Filter Bookings" description="Search by booking or client name, then narrow by status and schedule window.">
        <div class="grid gap-3 md:grid-cols-6">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search booking or client" class="ui-input">
            <select wire:model.live="status" class="ui-select">
                <option value="">All Status</option>
                @foreach (['pending', 'assigned', 'confirmed', 'completed', 'cancelled'] as $item)
                    <option value="{{ $item }}">{{ strtoupper($item) }}</option>
                @endforeach
            </select>
            <select wire:model.live="poolId" class="ui-select">
                <option value="">All Pool</option>
                @foreach (\App\Models\Pool::query()->orderBy('name')->get() as $pool)
                    <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                @endforeach
            </select>
            <input wire:model.live="startDateFrom" type="date" class="ui-input">
            <input wire:model.live="startDateTo" type="date" class="ui-input">
            <div class="flex items-end justify-end">
                <x-ui.action-button :href="route('bookings.index')" variant="ghost">Reset</x-ui.action-button>
            </div>
        </div>
    </x-ui.form-card>

    <x-ui.table-card title="Booking List" description="Operational view of booking demand and current dispatch status.">
        @if ($bookings->count() === 0)
            <div class="p-5">
                <x-ui.empty-state title="No bookings found" description="Try adjusting the filters or create a new booking to start the pipeline." />
            </div>
        @else
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th><button type="button" wire:click="sort('booking_number')" class="ui-sort-link {{ $sortBy === 'booking_number' ? 'is-active' : '' }}">Booking @if($sortBy==='booking_number')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('client_name')" class="ui-sort-link {{ $sortBy === 'client_name' ? 'is-active' : '' }}">Client @if($sortBy==='client_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('pool_name')" class="ui-sort-link {{ $sortBy === 'pool_name' ? 'is-active' : '' }}">Pool @if($sortBy==='pool_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('vehicle_name')" class="ui-sort-link {{ $sortBy === 'vehicle_name' ? 'is-active' : '' }}">Vehicle @if($sortBy==='vehicle_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('driver_name')" class="ui-sort-link {{ $sortBy === 'driver_name' ? 'is-active' : '' }}">Driver @if($sortBy==='driver_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('start_datetime')" class="ui-sort-link {{ $sortBy === 'start_datetime' ? 'is-active' : '' }}">Start @if($sortBy==='start_datetime')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('status')" class="ui-sort-link {{ $sortBy === 'status' ? 'is-active' : '' }}">Status @if($sortBy==='status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>
                                <a href="{{ route('bookings.show', $booking) }}" class="ui-link font-semibold text-slate-900">{{ $booking->booking_number }}</a>
                                <p class="mt-1 text-xs text-slate-500">{{ $booking->end_datetime?->format('Y-m-d H:i') ? 'Ends '.$booking->end_datetime?->format('Y-m-d H:i') : 'No end schedule' }}</p>
                            </td>
                            <td>
                                @if ($booking->client)
                                    <a href="{{ route('crm.clients.show', $booking->client) }}" class="ui-link">{{ $booking->client->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($booking->pool)
                                    <a href="{{ route('bookings.index', ['pool' => $booking->pool->id]) }}" class="ui-link">{{ $booking->pool->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($booking->vehicle)
                                    <a href="{{ route('fleet.vehicles.show', $booking->vehicle) }}" class="ui-link">{{ $booking->vehicle->plate_number }}</a>
                                    <p class="mt-1 text-xs text-slate-500">{{ trim($booking->vehicle->brand.' '.$booking->vehicle->model) }}</p>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($booking->driver)
                                    <a href="{{ route('drivers.show', $booking->driver) }}" class="ui-link">{{ $booking->driver->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $booking->start_datetime?->format('Y-m-d H:i') }}</td>
                            <td><a href="{{ route('bookings.index', ['status' => $booking->status]) }}"><x-ui.status-badge :status="$booking->status" /></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $bookings->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
