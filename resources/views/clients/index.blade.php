<x-layouts.app :title="'Clients'" :header="'CRM / Clients'">
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
        ['label' => 'CRM', 'url' => route('crm.index')],
        ['label' => 'Clients', 'url' => route('crm.clients.index')],
    ]" />

    <x-ui.page-header title="Client portfolio" eyebrow="CRM" description="Review account quality, search commercial entities quickly, and jump into account-level drill-down.">
        <x-slot:actions>
            @can('clients.create')
                <x-ui.action-button :href="route('crm.clients.create')" variant="primary">+ New Client</x-ui.action-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Filter clients" description="Search by client identity, then narrow the portfolio by tier and commercial status.">
        <form method="GET" class="grid gap-3 md:grid-cols-5">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name/legal name" class="ui-input">
            <select name="tier" class="ui-select"><option value="">All Tier</option>@foreach(['bronze','silver','gold','platinum'] as $tier)<option value="{{ $tier }}" @selected(($filters['tier'] ?? '')===$tier)>{{ strtoupper($tier) }}</option>@endforeach</select>
            <select name="status" class="ui-select"><option value="">All Status</option>@foreach(['active','inactive','prospect'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ strtoupper($status) }}</option>@endforeach</select>
            <div class="flex items-end"><x-ui.action-button type="submit" variant="primary">Apply Filter</x-ui.action-button></div>
            <div class="flex items-end justify-end"><x-ui.action-button :href="route('crm.clients.index')" variant="ghost">Reset</x-ui.action-button></div>
        </form>
    </x-ui.form-card>

    <x-ui.table-card title="Client list" description="Account-level view for sales, finance, and management drill-down.">
        @if($clients->count()===0)
            <div class="p-5"><x-ui.empty-state title="No clients found" description="Try broadening the filters or create a new account to start the CRM flow." /></div>
        @else
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr>
                <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? 'name') === 'name' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_dir' => $nextDir('name')]) }}">Name {{ $sortIndicator('name') }}</a></th>
                <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'tier' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'tier', 'sort_dir' => $nextDir('tier')]) }}">Tier {{ $sortIndicator('tier') }}</a></th>
                <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'status' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_dir' => $nextDir('status')]) }}">Status {{ $sortIndicator('status') }}</a></th>
                <th><a class="ui-sort-link {{ ($filters['sort_by'] ?? '') === 'contacts_count' ? 'is-active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort_by' => 'contacts_count', 'sort_dir' => $nextDir('contacts_count')]) }}">Contacts {{ $sortIndicator('contacts_count') }}</a></th>
            </tr></thead><tbody>
                @foreach($clients as $client)
                    <tr><td><a class="ui-link font-semibold text-slate-900" href="{{ route('crm.clients.show', $client) }}">{{ $client->name }}</a><p class="mt-1 text-xs text-slate-500">{{ $client->legal_name ?: '-' }}</p></td><td><a href="{{ request()->fullUrlWithQuery(['tier' => $client->tier]) }}"><x-ui.status-badge :status="$client->tier" /></a></td><td><a href="{{ request()->fullUrlWithQuery(['status' => $client->status]) }}"><x-ui.status-badge :status="$client->status" /></a></td><td>{{ $client->contacts_count }}</td></tr>
                @endforeach
            </tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $clients->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
