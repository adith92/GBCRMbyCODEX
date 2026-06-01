<x-layouts.app :title="'Driver Detail'" :header="'Driver Detail'">
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
            <a href="{{ route('drivers.index') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Back</a>
        </div>
    </section>
</x-layouts.app>
