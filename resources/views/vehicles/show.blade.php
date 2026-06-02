<x-layouts.app :title="'Vehicle Detail'" :header="'Vehicle Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Fleet', 'url' => route('fleet.index')],
        ['label' => 'Vehicles', 'url' => route('fleet.vehicles.index')],
        ['label' => $vehicle->plate_number, 'url' => route('fleet.vehicles.show', $vehicle)],
    ]" />

    <x-ui.page-header :title="$vehicle->plate_number" eyebrow="Vehicle Detail" description="Fleet metadata, booking linkage, and maintenance visibility for operational decisions.">
        <x-slot:actions>
            <x-ui.status-badge :status="$vehicle->status" />
            <x-back-link :fallback="route('fleet.vehicles.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    <section class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Current Pool" :value="$vehicle->pool?->name ?? '-'" hint="Pool ownership used by dispatch routing." tone="blue" />
        <x-ui.stat-card label="Bookings Linked" :value="$vehicle->bookings->count()" hint="Historical booking linkage for this unit." tone="emerald" />
        <x-ui.stat-card label="Maintenance Cases" :value="$vehicle->maintenanceLogs->count()" hint="Operational maintenance history for readiness tracking." tone="amber" />
    </section>

    <x-ui.form-card title="Vehicle snapshot" description="Read-only fleet details used by dispatch and maintenance teams.">
        <dl class="ui-meta-grid">
            <div class="ui-meta-item"><dt>Plate</dt><dd>{{ $vehicle->plate_number }}</dd></div>
            <div class="ui-meta-item"><dt>Pool</dt><dd>{{ $vehicle->pool?->name ?? '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Line</dt><dd>{{ strtoupper($vehicle->product_line) }}</dd></div>
            <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$vehicle->status" /></dd></div>
            <div class="ui-meta-item"><dt>Brand / Model</dt><dd>{{ $vehicle->brand }} {{ $vehicle->model }}</dd></div>
            <div class="ui-meta-item"><dt>Year</dt><dd>{{ $vehicle->year ?? '-' }}</dd></div>
        </dl>
        <div class="mt-5 flex flex-wrap gap-3">
            @can('vehicles.update')<x-ui.action-button :href="route('fleet.vehicles.edit', $vehicle)" variant="secondary">Edit</x-ui.action-button>@endcan
            @can('vehicles.delete')<form method="POST" action="{{ route('fleet.vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Delete this vehicle?')">@csrf @method('DELETE')<x-ui.action-button type="submit" variant="danger">Delete</x-ui.action-button></form>@endcan
        </div>
    </x-ui.form-card>

    <section class="grid gap-5 xl:grid-cols-2">
        <x-ui.table-card title="Booking History" description="Bookings that have been linked to this unit.">
            <div class="space-y-3 p-5 text-sm">
                @forelse($vehicle->bookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                        <div class="flex items-start justify-between gap-3"><p class="font-semibold text-slate-900">{{ $booking->booking_number }}</p><x-ui.status-badge :status="$booking->status" /></div>
                    </a>
                @empty
                    <x-ui.empty-state title="No bookings linked" description="Assignments involving this vehicle will appear here over time." />
                @endforelse
            </div>
        </x-ui.table-card>

        <x-ui.table-card title="Maintenance Logs" description="Workshop and service history for this vehicle.">
            <div class="space-y-3 p-5 text-sm">
                @forelse($vehicle->maintenanceLogs as $log)
                    <a href="{{ route('maintenance.show', $log) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-amber-200 hover:bg-amber-50/40">
                        <div class="flex items-start justify-between gap-3"><p class="font-semibold text-slate-900">{{ $log->title }}</p><x-ui.status-badge :status="$log->status" /></div>
                    </a>
                @empty
                    <x-ui.empty-state title="No maintenance logs yet" description="Operation-created maintenance cases will appear here." />
                @endforelse
            </div>
        </x-ui.table-card>
    </section>
</x-layouts.app>
