<x-layouts.app :title="'Pool Queue'" :header="'Pool / Dispatch Queue'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Pool Queue', 'url' => route('pool.queue')],
    ]" />

    <x-ui.page-header title="Dispatch queue" eyebrow="Pool Operations" description="Review bookings waiting for assignment and keep same-day dispatch activity under control." />

    <x-ui.form-card title="Filter Queue" description="Search by booking or client, then narrow down by pool and dispatch status.">
        <div class="grid gap-3 md:grid-cols-4">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search booking or client" class="ui-input">
            <select wire:model.live="status" class="ui-select">
                <option value="">All Status</option>
                <option value="pending">PENDING</option>
                <option value="assigned">ASSIGNED</option>
            </select>
            <select wire:model.live="pool_id" class="ui-select">
                <option value="">All Pools</option>
                @foreach ($pools as $pool)
                    <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                @endforeach
            </select>
            <div class="flex items-end justify-end">
                <x-ui.action-button :href="route('pool.queue')" variant="ghost">Reset</x-ui.action-button>
            </div>
        </div>
    </x-ui.form-card>

    <x-ui.table-card title="Queue List" description="Pending and assigned bookings that still need operational attention.">
        @if ($bookings->count() === 0)
            <div class="p-5">
                <x-ui.empty-state title="No booking queue found" description="The dispatch queue is currently clear for the selected filters." />
            </div>
        @else
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th><button type="button" wire:click="sort('booking_number')" class="ui-sort-link {{ $sortBy === 'booking_number' ? 'is-active' : '' }}">Booking @if($sortBy==='booking_number')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('client_name')" class="ui-sort-link {{ $sortBy === 'client_name' ? 'is-active' : '' }}">Client @if($sortBy==='client_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('pool_name')" class="ui-sort-link {{ $sortBy === 'pool_name' ? 'is-active' : '' }}">Pool @if($sortBy==='pool_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th><button type="button" wire:click="sort('start_datetime')" class="ui-sort-link {{ $sortBy === 'start_datetime' ? 'is-active' : '' }}">Schedule @if($sortBy==='start_datetime')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                        <th>Current Assignment</th>
                        <th><button type="button" wire:click="sort('status')" class="ui-sort-link {{ $sortBy === 'status' ? 'is-active' : '' }}">Status @if($sortBy==='status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>
                                <a href="{{ route('bookings.show', $booking) }}" class="ui-link font-semibold text-slate-900">{{ $booking->booking_number }}</a>
                            </td>
                            <td>
                                @if ($booking->client)
                                    <a href="{{ route('crm.clients.show', $booking->client) }}" class="ui-link">{{ $booking->client->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>@if($booking->pool)<a href="{{ route('pool.queue', ['pool' => $booking->pool->id]) }}" class="ui-link">{{ $booking->pool->name }}</a>@else - @endif</td>
                            <td>{{ $booking->start_datetime?->format('Y-m-d H:i') }}<br><span class="text-xs text-slate-500">to {{ $booking->end_datetime?->format('Y-m-d H:i') }}</span></td>
                            <td>
                                <p><span class="text-xs uppercase tracking-[0.14em] text-slate-500">Vehicle</span><br>@if($booking->vehicle)<a href="{{ route('fleet.vehicles.show', $booking->vehicle) }}" class="ui-link">{{ $booking->vehicle->plate_number }}</a>@else - @endif</p>
                                <p class="mt-2"><span class="text-xs uppercase tracking-[0.14em] text-slate-500">Driver</span><br>@if($booking->driver)<a href="{{ route('drivers.show', $booking->driver) }}" class="ui-link">{{ $booking->driver->name }}</a>@else - @endif</p>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('pool.queue', ['status' => $booking->status]) }}"><x-ui.status-badge :status="$booking->status" /></a>
                                @can('pool.assign-driver')
                                    <x-ui.action-button :href="route('pool.assign', $booking)" variant="secondary">Assign</x-ui.action-button>
                                @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $bookings->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
