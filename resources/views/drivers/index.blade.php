<x-layouts.app :title="'Drivers'" :header="'Drivers'">
    @php
        $nextDir = function (string $field) use ($filters): string {
            return (($filters['sort_by'] ?? 'name') === $field && ($filters['sort_dir'] ?? 'asc') === 'asc') ? 'desc' : 'asc';
        };
        $sortIndicator = function (string $field) use ($filters): string {
            if (($filters['sort_by'] ?? 'name') !== $field) {
                return '';
            }
            return ($filters['sort_dir'] ?? 'asc') === 'asc' ? '↑' : '↓';
        };
    @endphp

    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Drivers', 'url' => route('drivers.index')],
    ]" />

    <x-ui.page-header title="Drivers" eyebrow="Operations" description="Search crew quickly, sort operationally, and jump straight into driver detail.">
        <x-slot:actions>
            @can('drivers.create')
                <x-ui.action-button :href="route('drivers.create')" variant="primary">+ Driver</x-ui.action-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Filter Drivers" description="Search by name, phone, or employee code, then narrow by pool and status.">
        <form method="GET" class="grid gap-3 md:grid-cols-5">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name/phone/employee" class="ui-input">
            <select name="status" class="ui-select"><option value="">All Status</option>@foreach(['active','inactive','sick','on_leave'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ strtoupper($status) }}</option>@endforeach</select>
            <select name="pool_id" class="ui-select"><option value="">All Pool</option>@foreach($pools as $pool)<option value="{{ $pool->id }}" @selected(($filters['pool_id'] ?? '')==$pool->id)>{{ $pool->name }}</option>@endforeach</select>
            <div class="flex items-end"><x-ui.action-button type="submit" variant="primary">Apply Filter</x-ui.action-button></div>
            <div class="flex items-end justify-end"><x-ui.action-button :href="route('drivers.index')" variant="ghost">Reset</x-ui.action-button></div>
        </form>
    </x-ui.form-card>

    <x-ui.table-card title="Driver List" description="Operational roster with direct navigation into driver detail and assignment history.">
        @if($drivers->count()===0)
            <div class="p-5"><x-ui.empty-state title="No drivers found" description="Try broadening the filters or add a new driver to extend operational capacity." /></div>
        @else
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr>
                <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? 'name') === 'name' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_dir' => $nextDir('name')]) }}">Name {{ $sortIndicator('name') }}</a></th>
                <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'pool_name' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'pool_name', 'sort_dir' => $nextDir('pool_name')]) }}">Pool {{ $sortIndicator('pool_name') }}</a></th>
                <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'status' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_dir' => $nextDir('status')]) }}">Status {{ $sortIndicator('status') }}</a></th>
                <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'license_expired_at' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'license_expired_at', 'sort_dir' => $nextDir('license_expired_at')]) }}">License {{ $sortIndicator('license_expired_at') }}</a></th>
            </tr></thead><tbody>
                @foreach($drivers as $driver)
                @php $isExpired = $driver->license_expired_at && $driver->license_expired_at->isPast(); @endphp
                <tr>
                    <td class="px-4 py-3"><a class="ui-link font-semibold text-slate-900" href="{{ route('drivers.show', $driver) }}">{{ $driver->name }}</a><div class="text-xs text-slate-500">{{ $driver->employee_code ?: '-' }}</div></td>
                    <td class="px-4 py-3">@if($driver->pool)<a class="ui-link" href="{{ request()->fullUrlWithQuery(['pool_id' => $driver->pool->id]) }}">{{ $driver->pool->name }}</a>@else - @endif</td>
                    <td class="px-4 py-3"><a href="{{ request()->fullUrlWithQuery(['status' => $driver->status]) }}"><x-ui.status-badge :status="$driver->status" /></a></td>
                    <td class="px-4 py-3">@if($driver->license_expired_at)<span class="text-xs {{ $isExpired ? 'text-red-600' : 'text-slate-600' }}">{{ $driver->license_expired_at->format('Y-m-d') }} {{ $isExpired ? '(EXPIRED)' : '' }}</span>@else-@endif</td>
                </tr>
                @endforeach
            </tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $drivers->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
