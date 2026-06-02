<x-layouts.app :title="'Maintenance Detail'" :header="'Maintenance / Detail'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <section class="rounded-lg border bg-white p-4 text-sm">
        <div class="grid gap-2 md:grid-cols-2">
            <p><strong>Title:</strong> {{ $maintenanceLog->title }}</p>
            <p><strong>Status:</strong> <span class="uppercase text-xs">{{ $maintenanceLog->status }}</span></p>
            <p><strong>Vehicle:</strong> <a href="{{ route('fleet.vehicles.show', $maintenanceLog->vehicle) }}" class="text-blue-600 hover:underline">{{ $maintenanceLog->vehicle?->plate_number }}</a></p>
            <p><strong>Reported By:</strong> {{ $maintenanceLog->reportedBy?->name ?? '-' }}</p>
            <p><strong>Start At:</strong> {{ $maintenanceLog->start_at?->format('Y-m-d H:i') ?? '-' }}</p>
            <p><strong>End At:</strong> {{ $maintenanceLog->end_at?->format('Y-m-d H:i') ?? '-' }}</p>
            <p><strong>Cost:</strong> {{ number_format($maintenanceLog->cost, 2) }}</p>
        </div>
        <p class="mt-3"><strong>Notes:</strong> {{ $maintenanceLog->notes ?: '-' }}</p>

        <div class="mt-4 flex gap-2">
            @can('maintenance.update')
                <a href="{{ route('maintenance.edit', $maintenanceLog) }}" class="rounded border border-slate-300 px-3 py-2 text-sm">Edit</a>
            @endcan
            <x-back-link :fallback="route('maintenance.index')" />
        </div>
    </section>
</x-layouts.app>
