<x-layouts.app :title="'Maintenance'" :header="'Maintenance'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <x-ui.page-header title="Maintenance control" eyebrow="Maintenance" description="Coordinate service activity without losing fleet availability awareness.">
        <x-slot:actions>
            @can('maintenance.create')
                <x-ui.action-button :href="route('maintenance.create')" variant="primary">+ Maintenance</x-ui.action-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Filter Maintenance" description="Search by title or plate number, then narrow down by vehicle and maintenance state.">
        <div class="grid gap-3 md:grid-cols-4">
            <input wire:model.live.debounce.300ms="search" placeholder="Search title or plate" class="ui-input">
            <select wire:model.live="status" class="ui-select">
                <option value="">All Status</option>
                @foreach (['scheduled', 'in_progress', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}">{{ strtoupper($status) }}</option>
                @endforeach
            </select>
            <select wire:model.live="vehicle_id" class="ui-select">
                <option value="">All Vehicles</option>
                @foreach ($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }}</option>
                @endforeach
            </select>
            <div class="flex items-end justify-end"><x-ui.action-button :href="route('maintenance.index')" variant="ghost">Reset</x-ui.action-button></div>
        </div>
    </x-ui.form-card>

    <x-ui.table-card title="Maintenance Logs" description="Track workshop workload and release timing for each vehicle.">
        @if($maintenanceLogs->count() === 0)
            <div class="p-5"><x-ui.empty-state title="No maintenance logs found" description="Create a maintenance case when a vehicle needs inspection, repair, or workshop scheduling." /></div>
        @else
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                        <th>Cost</th>
                        <th class="text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($maintenanceLogs as $log)
                        <tr>
                            <td>{{ $log->title }}</td>
                            <td>{{ $log->vehicle?->plate_number }}</td>
                            <td><x-ui.status-badge :status="$log->status" /></td>
                            <td>{{ number_format($log->cost, 2) }}</td>
                            <td class="text-right"><a class="ui-link" href="{{ route('maintenance.show', $log) }}">Open Detail</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $maintenanceLogs->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
