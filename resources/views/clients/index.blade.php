<x-layouts.app :title="'Clients'" :header="'CRM / Clients'">
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
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr><th>Name</th><th>Tier</th><th>Status</th><th>Contacts</th><th class="text-right">Action</th></tr></thead><tbody>
                @foreach($clients as $client)
                    <tr><td><p class="font-semibold text-slate-900">{{ $client->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $client->legal_name ?: '-' }}</p></td><td><x-ui.status-badge :status="$client->tier" /></td><td><x-ui.status-badge :status="$client->status" /></td><td>{{ $client->contacts_count }}</td><td class="text-right"><a class="ui-link" href="{{ route('crm.clients.show', $client) }}">Open Detail</a></td></tr>
                @endforeach
            </tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $clients->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
