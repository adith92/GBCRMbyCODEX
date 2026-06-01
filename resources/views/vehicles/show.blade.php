<x-layouts.app :title="'Vehicle Detail'" :header="'Vehicle Detail'">
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
            <a href="{{ route('fleet.vehicles.index') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Back</a>
        </div>
    </section>
</x-layouts.app>
