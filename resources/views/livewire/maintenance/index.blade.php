<x-layouts.app :title="'Maintenance'" :header="'Maintenance'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <section class="rounded-lg border bg-white p-4">
        <div class="grid gap-3 md:grid-cols-4">
            <input wire:model.live.debounce.300ms="search" placeholder="Search title or plate" class="rounded border-slate-300 text-sm">
            <select wire:model.live="status" class="rounded border-slate-300 text-sm">
                <option value="">All Status</option>
                @foreach (['scheduled', 'in_progress', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}">{{ strtoupper($status) }}</option>
                @endforeach
            </select>
            <select wire:model.live="vehicle_id" class="rounded border-slate-300 text-sm">
                <option value="">All Vehicles</option>
                @foreach ($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }}</option>
                @endforeach
            </select>
            <div class="text-right">
                @can('maintenance.create')
                    <a href="{{ route('maintenance.create') }}" class="rounded bg-slate-900 px-3 py-2 text-sm text-white">+ Maintenance</a>
                @endcan
            </div>
        </div>
    </section>

    <section class="rounded-lg border bg-white">
        @if($maintenanceLogs->count() === 0)
            <p class="p-6 text-sm text-slate-500">No maintenance logs found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Title</th>
                        <th class="px-4 py-3 text-left">Vehicle</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Cost</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($maintenanceLogs as $log)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $log->title }}</td>
                            <td class="px-4 py-3">{{ $log->vehicle?->plate_number }}</td>
                            <td class="px-4 py-3 uppercase text-xs">{{ $log->status }}</td>
                            <td class="px-4 py-3">{{ number_format($log->cost, 2) }}</td>
                            <td class="px-4 py-3 text-right"><a class="text-blue-600" href="{{ route('maintenance.show', $log) }}">Detail</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $maintenanceLogs->links() }}</div>
        @endif
    </section>
</x-layouts.app>
