<x-layouts.app :title="'HR Licenses'" :header="'HR / Licenses'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <section class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-lg border bg-white p-4">
            <h3 class="text-sm font-semibold">Expired Licenses</h3>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($expiredDrivers as $driver)
                    <div class="rounded border p-3">
                        {{ $driver->name }}
                        <span class="float-right">{{ $driver->license_expired_at?->format('Y-m-d') }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No expired licenses.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-lg border bg-white p-4">
            <h3 class="text-sm font-semibold">Expiring Within 30 Days</h3>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($expiringDrivers as $driver)
                    <div class="rounded border p-3">
                        {{ $driver->name }}
                        <span class="float-right">{{ $driver->license_expired_at?->format('Y-m-d') }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No upcoming expirations.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.app>
