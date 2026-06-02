<x-layouts.app :title="'Vehicle Detail'" :header="'Vehicle Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Fleet', 'url' => route('fleet.index')],
        ['label' => 'Vehicles', 'url' => route('fleet.vehicles.index')],
        ['label' => $vehicle->plate_number, 'url' => route('fleet.vehicles.show', $vehicle)],
    ]" />

    <section class="rounded-lg border border-slate-200 bg-white p-4 text-sm">
        <div class="grid gap-2 md:grid-cols-2">
            <p><strong>Plate:</strong> {{ $vehicle->plate_number }}</p>
            <p><strong>Pool:</strong> {{ $vehicle->pool?->name ?? '-' }}</p>
            <p><strong>Line:</strong> {{ strtoupper($vehicle->product_line) }}</p>
            <p><strong>Status:</strong> {{ strtoupper($vehicle->status) }}</p>
            <p><strong>Brand/Model:</strong> {{ $vehicle->brand }} {{ $vehicle->model }}</p>
            <p><strong>Year:</strong> {{ $vehicle->year ?? '-' }}</p>
        </div>
        <div class="mt-4 flex gap-2">
            @can('vehicles.update')<a href="{{ route('fleet.vehicles.edit', $vehicle) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Edit</a>@endcan
            @can('vehicles.delete')
            <form method="POST" action="{{ route('fleet.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Delete this vehicle?')">
                @csrf @method('DELETE')
                <button class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-600">Delete</button>
            </form>
            @endcan
            <x-back-link :fallback="route('fleet.vehicles.index')" />
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <h3 class="text-base font-semibold">Booking History</h3>
        <div class="mt-3 space-y-2 text-sm">
            @forelse($vehicle->bookings as $booking)
                <a href="{{ route('bookings.show', $booking) }}" class="block rounded border p-3 hover:bg-slate-50">
                    {{ $booking->booking_number }}
                    <span class="float-right uppercase text-xs">{{ $booking->status }}</span>
                </a>
            @empty
                <p class="text-slate-500">No bookings linked to this vehicle.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <h3 class="text-base font-semibold">Maintenance Logs</h3>
        <div class="mt-3 space-y-2 text-sm">
            @forelse($vehicle->maintenanceLogs as $log)
                <a href="{{ route('maintenance.show', $log) }}" class="block rounded border p-3 hover:bg-slate-50">
                    {{ $log->title }}
                    <span class="float-right uppercase text-xs">{{ $log->status }}</span>
                </a>
            @empty
                <p class="text-slate-500">No maintenance logs yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
