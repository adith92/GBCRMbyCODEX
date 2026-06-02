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
                        <th>Booking</th>
                        <th>Client</th>
                        <th>Pool</th>
                        <th>Schedule</th>
                        <th>Current Assignment</th>
                        <th class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $booking->booking_number }}</p>
                                <div class="mt-2"><x-ui.status-badge :status="$booking->status" /></div>
                            </td>
                            <td>
                                @if ($booking->client)
                                    <a href="{{ route('crm.clients.show', $booking->client) }}" class="ui-link">{{ $booking->client->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $booking->pool?->name ?? '-' }}</td>
                            <td>{{ $booking->start_datetime?->format('Y-m-d H:i') }}<br><span class="text-xs text-slate-500">to {{ $booking->end_datetime?->format('Y-m-d H:i') }}</span></td>
                            <td>
                                <p><span class="text-xs uppercase tracking-[0.14em] text-slate-500">Vehicle</span><br>{{ $booking->vehicle?->plate_number ?? '-' }}</p>
                                <p class="mt-2"><span class="text-xs uppercase tracking-[0.14em] text-slate-500">Driver</span><br>{{ $booking->driver?->name ?? '-' }}</p>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('bookings.show', $booking) }}" class="ui-link mr-3">Detail</a>
                                @can('pool.assign-driver')
                                    <x-ui.action-button :href="route('pool.assign', $booking)" variant="secondary">Assign</x-ui.action-button>
                                @endcan
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
