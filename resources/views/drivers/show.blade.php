<x-layouts.app :title="'Driver Detail'" :header="'Driver Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Drivers', 'url' => route('drivers.index')],
        ['label' => $driver->name, 'url' => route('drivers.show', $driver)],
    ]" />

    <section class="rounded-lg border border-slate-200 bg-white p-4 text-sm">
        <div class="grid gap-2 md:grid-cols-2">
            <p><strong>Name:</strong> {{ $driver->name }}</p><p><strong>Pool:</strong> {{ $driver->pool?->name ?? '-' }}</p>
            <p><strong>Employee Code:</strong> {{ $driver->employee_code ?: '-' }}</p><p><strong>Status:</strong> {{ strtoupper($driver->status) }}</p>
            <p><strong>Phone:</strong> {{ $driver->phone ?: '-' }}</p><p><strong>Email:</strong> {{ $driver->email ?: '-' }}</p>
            <p><strong>License:</strong> {{ $driver->license_type ?: '-' }} / {{ $driver->license_number ?: '-' }}</p><p><strong>Expired:</strong> {{ $driver->license_expired_at?->format('Y-m-d') ?: '-' }}</p>
        </div>
        <div class="mt-4 flex gap-2">
            @can('drivers.update')<a href="{{ route('drivers.edit', $driver) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Edit</a>@endcan
            @can('drivers.delete')<form method="POST" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm('Delete this driver?')">@csrf @method('DELETE')<button class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-600">Delete</button></form>@endcan
            <x-back-link :fallback="route('drivers.index')" />
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <h3 class="text-base font-semibold">Assignments & Bookings</h3>
        <div class="mt-3 space-y-2 text-sm">
            @forelse($driver->bookings as $booking)
                <a href="{{ route('bookings.show', $booking) }}" class="block rounded border p-3 hover:bg-slate-50">
                    {{ $booking->booking_number }}
                    <span class="float-right uppercase text-xs">{{ $booking->status }}</span>
                </a>
            @empty
                <p class="text-slate-500">No bookings for this driver.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
