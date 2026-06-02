<x-layouts.app :title="'HR Drivers'" :header="'HR / Drivers'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <x-ui.page-header title="Driver master records" eyebrow="HR Backend Only" description="Restricted view for driver identity, pool placement, and license validity monitoring." />

    <x-ui.form-card title="Filter Drivers" description="Search by name, employee code, or license number.">
        <input wire:model.live.debounce.300ms="search" placeholder="Search driver name" class="ui-input">
    </x-ui.form-card>

    <x-ui.table-card title="Driver Registry" description="Super-admin operational HR view of driver records.">
        <div class="ui-table-wrap">
            <table class="ui-table">
                <thead>
                <tr>
                    <th>Driver</th>
                    <th>Pool</th>
                    <th>Status</th>
                    <th>License Expired At</th>
                </tr>
                </thead>
                <tbody>
                @foreach($drivers as $driver)
                    <tr>
                        <td><a href="{{ route('drivers.show', $driver) }}" class="ui-link">{{ $driver->name }}</a></td>
                        <td>{{ $driver->pool?->name ?? '-' }}</td>
                        <td><x-ui.status-badge :status="$driver->status" /></td>
                        <td>{{ $driver->license_expired_at?->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200/80 px-4 py-4">{{ $drivers->links() }}</div>
    </x-ui.table-card>
</x-layouts.app>
