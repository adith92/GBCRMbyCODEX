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
        <div class="grid gap-3 md:grid-cols-5">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search booking or client" class="ui-input">
            <select wire:model.live="status" class="ui-select">
                <option value="">All Status</option>
                @foreach (['pending', 'assigned', 'confirmed', 'completed', 'cancelled'] as $item)
                    <option value="{{ $item }}">{{ strtoupper($item) }}</option>
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
                        <th>Booking</th>
                        <th>Client</th>
                        <th>Pool</th>
                        <th>Start</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $booking->booking_number }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $booking->end_datetime?->format('Y-m-d H:i') ? 'Ends '.$booking->end_datetime?->format('Y-m-d H:i') : 'No end schedule' }}</p>
                            </td>
                            <td>
                                @if ($booking->client)
                                    <a href="{{ route('crm.clients.show', $booking->client) }}" class="ui-link">{{ $booking->client->name }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $booking->pool?->name ?? '-' }}</td>
                            <td>{{ $booking->start_datetime?->format('Y-m-d H:i') }}</td>
                            <td><x-ui.status-badge :status="$booking->status" /></td>
                            <td class="text-right"><a href="{{ route('bookings.show', $booking) }}" class="ui-link">Open Detail</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $bookings->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
