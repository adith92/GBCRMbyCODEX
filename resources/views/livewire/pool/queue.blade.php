<x-layouts.app :title="'Pool Queue'" :header="'Pool / Dispatch Queue'">
    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <div class="grid gap-3 md:grid-cols-4">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search booking/client" class="rounded-md border-slate-300 text-sm">
            <select wire:model.live="status" class="rounded-md border-slate-300 text-sm">
                <option value="">All Status</option>
                <option value="pending">PENDING</option>
                <option value="assigned">ASSIGNED</option>
            </select>
            <select wire:model.live="pool_id" class="rounded-md border-slate-300 text-sm">
                <option value="">All Pools</option>
                @foreach ($pools as $pool)
                    <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                @endforeach
            </select>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white">
        @if ($bookings->count() === 0)
            <p class="p-6 text-sm text-slate-500">No booking queue found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Booking</th>
                        <th class="px-4 py-3 text-left">Client</th>
                        <th class="px-4 py-3 text-left">Pool</th>
                        <th class="px-4 py-3 text-left">Schedule</th>
                        <th class="px-4 py-3 text-left">Current Assignment</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($bookings as $booking)
                        <tr class="border-t">
                            <td class="px-4 py-3 font-medium">{{ $booking->booking_number }}</td>
                            <td class="px-4 py-3">{{ $booking->client?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $booking->pool?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $booking->start_datetime?->format('Y-m-d H:i') }} - {{ $booking->end_datetime?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <p>Vehicle: {{ $booking->vehicle?->plate_number ?? '-' }}</p>
                                <p>Driver: {{ $booking->driver?->name ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @can('pool.assign-driver')
                                    <a href="{{ route('pool.assign', $booking) }}" class="text-blue-600">Assign</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $bookings->links() }}</div>
        @endif
    </section>
</x-layouts.app>
