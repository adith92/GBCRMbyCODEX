<x-layouts.app :title="'Vehicles'" :header="'Fleet / Vehicles'">
    @php
        $nextDir = function (string $field) use ($filters): string {
            return (($filters['sort_by'] ?? 'plate_number') === $field && ($filters['sort_dir'] ?? 'asc') === 'asc') ? 'desc' : 'asc';
        };
        $sortIndicator = function (string $field) use ($filters): string {
            if (($filters['sort_by'] ?? 'plate_number') !== $field) {
                return '';
            }
            return ($filters['sort_dir'] ?? 'asc') === 'asc' ? '↑' : '↓';
        };
    @endphp
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Fleet', 'url' => route('fleet.index')],
        ['label' => 'Vehicles', 'url' => route('fleet.vehicles.index')],
    ]" />

    <x-ui.page-header title="Fleet vehicles" eyebrow="Fleet" description="Track readiness, search units quickly, and move deeper into assignment or maintenance context.">
        <x-slot:actions>
            @can('vehicles.create')<x-ui.action-button :href="route('fleet.vehicles.create')" variant="primary">+ New Vehicle</x-ui.action-button>@endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Filter fleet" description="Search by identity, then narrow units by operational status and pool.">
        <form method="GET" class="grid gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search plate/brand/model" class="ui-input">
            <select name="status" class="ui-select"><option value="">All Status</option>@foreach(['available','po','maintenance','hold'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ strtoupper($status) }}</option>@endforeach</select>
            <select name="pool_id" class="ui-select"><option value="">All Pool</option>@foreach($pools as $pool)<option value="{{ $pool->id }}" @selected(($filters['pool_id'] ?? '')==$pool->id)>{{ $pool->name }}</option>@endforeach</select>
            <div class="flex items-end justify-between gap-3"><x-ui.action-button type="submit" variant="primary">Apply Filter</x-ui.action-button><x-ui.action-button :href="route('fleet.vehicles.index')" variant="ghost">Reset</x-ui.action-button></div>
        </form>
    </x-ui.form-card>

    <x-ui.table-card title="Vehicle list" description="Operational fleet view with drill-down into detail, booking history, and maintenance state.">
        @if($vehicles->count() === 0)
            <div class="p-5"><x-ui.empty-state title="No vehicles found" description="Try broadening the filters or add a new vehicle to expand the fleet view." /></div>
        @else
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead><tr>
                        <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? 'plate_number') === 'plate_number' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'plate_number', 'sort_dir' => $nextDir('plate_number')]) }}">Plate {{ $sortIndicator('plate_number') }}</a></th>
                        <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'pool_name' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'pool_name', 'sort_dir' => $nextDir('pool_name')]) }}">Pool {{ $sortIndicator('pool_name') }}</a></th>
                        <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'brand' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'brand', 'sort_dir' => $nextDir('brand')]) }}">Vehicle {{ $sortIndicator('brand') }}</a></th>
                        <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'status' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_dir' => $nextDir('status')]) }}">Status {{ $sortIndicator('status') }}</a></th>
                    </tr></thead>
                    <tbody>
                    @foreach($vehicles as $vehicle)
                        <tr>
                            <td class="font-semibold text-slate-900"><a class="ui-link font-semibold text-slate-900" href="{{ route('fleet.vehicles.show', $vehicle) }}">{{ $vehicle->plate_number }}</a></td>
                            <td>@if($vehicle->pool)<a class="ui-link" href="{{ request()->fullUrlWithQuery(['pool_id' => $vehicle->pool->id]) }}">{{ $vehicle->pool->name }}</a>@else - @endif</td>
                            <td><a class="ui-link" href="{{ route('fleet.vehicles.show', $vehicle) }}">{{ $vehicle->brand }} {{ $vehicle->model }}</a></td>
                            <td><a href="{{ request()->fullUrlWithQuery(['status' => $vehicle->status]) }}"><x-ui.status-badge :status="$vehicle->status" /></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $vehicles->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
