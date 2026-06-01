<x-layouts.app :title="'Booking Detail'" :header="'Bookings / Detail'">
    @if ($errorMessage)
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errorMessage }}</div>
    @endif

    <section class="rounded-lg border border-slate-200 bg-white p-4 text-sm">
        <div class="grid gap-2 md:grid-cols-2">
            <p><strong>Booking Number:</strong> {{ $booking->booking_number }}</p>
            <p><strong>Status:</strong> <span class="rounded-full bg-slate-100 px-2 py-1 text-xs uppercase">{{ $booking->status }}</span></p>
            <p><strong>Client:</strong> {{ $booking->client?->name ?? '-' }}</p>
            <p><strong>Pool:</strong> {{ $booking->pool?->name ?? '-' }}</p>
            <p><strong>Vehicle:</strong> {{ $booking->vehicle?->plate_number ?? '-' }}</p>
            <p><strong>Driver:</strong> {{ $booking->driver?->name ?? '-' }}</p>
            <p><strong>Start:</strong> {{ $booking->start_datetime?->format('Y-m-d H:i') }}</p>
            <p><strong>End:</strong> {{ $booking->end_datetime?->format('Y-m-d H:i') }}</p>
        </div>
        <p class="mt-3"><strong>Pickup:</strong> {{ $booking->pickup_location ?: '-' }}</p>
        <p class="mt-1"><strong>Destination:</strong> {{ $booking->destination ?: '-' }}</p>
        <p class="mt-1"><strong>Notes:</strong> {{ $booking->notes ?: '-' }}</p>

        <div class="mt-4 flex flex-wrap gap-2">
            @can('bookings.update')
                <a href="{{ route('bookings.edit', $booking) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Edit</a>
            @endcan
            @can('pool.assign-driver')
                @if (in_array($booking->status, ['pending', 'assigned'], true))
                    <a href="{{ route('pool.assign', $booking) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Assign Driver & Vehicle</a>
                @endif
            @endcan
            @can('bookings.approve')
                @if ($booking->status === 'assigned')
                    <button wire:click="confirm" wire:confirm="Confirm this booking?" class="rounded-md bg-emerald-600 px-3 py-2 text-sm text-white">Confirm</button>
                @endif
            @endcan
            @can('bookings.cancel')
                @if (in_array($booking->status, ['pending', 'assigned', 'confirmed'], true))
                    <button wire:click="cancel" wire:confirm="Cancel this booking?" class="rounded-md bg-red-600 px-3 py-2 text-sm text-white">Cancel</button>
                @endif
            @endcan
            <a href="{{ route('bookings.index') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Back</a>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <h3 class="text-base font-semibold">Driver Assignment History</h3>
        @if($booking->driverAssignments->isEmpty())
            <p class="mt-2 text-sm text-slate-500">No assignment history yet.</p>
        @else
            <div class="mt-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Assigned At</th>
                        <th class="px-4 py-3 text-left">Driver</th>
                        <th class="px-4 py-3 text-left">Vehicle</th>
                        <th class="px-4 py-3 text-left">By</th>
                        <th class="px-4 py-3 text-left">Released</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($booking->driverAssignments as $assignment)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $assignment->assigned_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">{{ $assignment->driver?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $assignment->vehicle?->plate_number ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $assignment->assignedBy?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $assignment->released_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.app>
