<x-layouts.app :title="'Driver Detail'" :header="'Driver Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Drivers', 'url' => route('drivers.index')],
        ['label' => $driver->name, 'url' => route('drivers.show', $driver)],
    ]" />

    <x-ui.page-header :title="$driver->name" eyebrow="Driver Detail" description="Driver identity, readiness, and recent assignment context for dispatch operations.">
        <x-slot:actions>
            <x-ui.status-badge :status="$driver->status" />
            <x-back-link :fallback="route('drivers.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    <section class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Current Pool" :value="$driver->pool?->name ?? '-'" hint="Pool ownership used by dispatch and scheduling." tone="blue" />
        <x-ui.stat-card label="Bookings Linked" :value="$driver->bookings->count()" hint="Recent booking linkage handled by this driver." tone="emerald" />
        <x-ui.stat-card label="License Expiry" :value="$driver->license_expired_at?->format('Y-m-d') ?: '-'" hint="Compliance date surfaced for operational readiness." tone="amber" />
    </section>

    <x-ui.form-card title="Driver snapshot" description="Core driver profile, contactability, and license readiness.">
        <dl class="ui-meta-grid">
            <div class="ui-meta-item"><dt>Name</dt><dd>{{ $driver->name }}</dd></div>
            <div class="ui-meta-item"><dt>Pool</dt><dd>{{ $driver->pool?->name ?? '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Employee Code</dt><dd>{{ $driver->employee_code ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$driver->status" /></dd></div>
            <div class="ui-meta-item"><dt>Phone</dt><dd>{{ $driver->phone ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Email</dt><dd>{{ $driver->email ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>License</dt><dd>{{ $driver->license_type ?: '-' }} / {{ $driver->license_number ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Expired</dt><dd>{{ $driver->license_expired_at?->format('Y-m-d') ?: '-' }}</dd></div>
        </dl>
        <div class="mt-5 flex flex-wrap gap-3">
            @can('drivers.update')<x-ui.action-button :href="route('drivers.edit', $driver)" variant="secondary">Edit</x-ui.action-button>@endcan
            @can('drivers.delete')<form method="POST" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm('Delete this driver?')">@csrf @method('DELETE')<x-ui.action-button type="submit" variant="danger">Delete</x-ui.action-button></form>@endcan
        </div>
    </x-ui.form-card>

    <x-ui.table-card title="Assignments & Bookings" description="Recent booking assignments handled by this driver.">
        <div class="space-y-3 p-5 text-sm">
            @forelse($driver->bookings as $booking)
                <a href="{{ route('bookings.show', $booking) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $booking->booking_number }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $booking->client?->name ?? 'No client linked' }}</p>
                        </div>
                        <x-ui.status-badge :status="$booking->status" />
                    </div>
                </a>
            @empty
                <x-ui.empty-state title="No bookings for this driver" description="Assignments linked to this driver will surface here automatically." />
            @endforelse
        </div>
    </x-ui.table-card>
</x-layouts.app>
