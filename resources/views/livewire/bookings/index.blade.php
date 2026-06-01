<x-layouts.app :title="'Bookings'" :header="'Bookings'">
    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <div class="grid gap-3 md:grid-cols-5">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search booking/client" class="rounded-md border-slate-300 text-sm">
            <select wire:model.live="status" class="rounded-md border-slate-300 text-sm">
                <option value="">All Status</option>
                @foreach (['pending', 'assigned', 'confirmed', 'completed', 'cancelled'] as $item)
                    <option value="{{ $item }}">{{ strtoupper($item) }}</option>
                @endforeach
            </select>
            <input wire:model.live="startDateFrom" type="date" class="rounded-md border-slate-300 text-sm">
            <input wire:model.live="startDateTo" type="date" class="rounded-md border-slate-300 text-sm">
            <div class="flex items-center justify-end">
                @can('bookings.create')
                    <a href="{{ route('bookings.create') }}" class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">+ Booking</a>
                @endcan
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white">
        @if ($bookings->count() === 0)
            <p class="p-6 text-sm text-slate-500">No bookings found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Booking</th>
                        <th class="px-4 py-3 text-left">Client</th>
                        <th class="px-4 py-3 text-left">Pool</th>
                        <th class="px-4 py-3 text-left">Start</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($bookings as $booking)
                        <tr class="border-t">
                            <td class="px-4 py-3 font-medium">{{ $booking->booking_number }}</td>
                            <td class="px-4 py-3">{{ $booking->client?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $booking->pool?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $booking->start_datetime?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs uppercase text-slate-700">{{ $booking->status }}</span></td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('bookings.show', $booking) }}" class="text-blue-600">Detail</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $bookings->links() }}</div>
        @endif
    </section>
</x-layouts.app>
