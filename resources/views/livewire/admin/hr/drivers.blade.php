<x-layouts.app :title="'HR Drivers'" :header="'HR / Drivers'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <section class="rounded-lg border bg-white p-4">
        <input wire:model.live.debounce.300ms="search" placeholder="Search driver name" class="w-full rounded border-slate-300 text-sm">
    </section>

    <section class="rounded-lg border bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left">Driver</th>
                    <th class="px-4 py-3 text-left">Pool</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">License Expired At</th>
                </tr>
                </thead>
                <tbody>
                @foreach($drivers as $driver)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $driver->name }}</td>
                        <td class="px-4 py-3">{{ $driver->pool?->name ?? '-' }}</td>
                        <td class="px-4 py-3 uppercase text-xs">{{ $driver->status }}</td>
                        <td class="px-4 py-3">{{ $driver->license_expired_at?->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $drivers->links() }}</div>
    </section>
</x-layouts.app>
