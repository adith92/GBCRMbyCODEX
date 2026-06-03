<x-layouts.app :title="'Purchase Orders'" :header="'Finance / Purchase Orders'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Finance', 'url' => route('finance.index')],
        ['label' => 'Purchase Orders', 'url' => route('finance.purchase-orders.index')],
    ]" />

    <x-ui.page-header title="Purchase orders" eyebrow="Finance" description="Track purchase orders created from confirmed bookings and move them toward invoicing.">
        <x-slot:actions>
            @can('purchase-orders.create')
                <x-ui.action-button :href="route('finance.purchase-orders.create')" variant="primary">+ New PO</x-ui.action-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Filter Purchase Orders" description="Filter by PO number, document status, or client account.">
        <div class="grid gap-3 md:grid-cols-4">
            <input wire:model.live.debounce.300ms="search" placeholder="Search PO number" class="ui-input">
            <select wire:model.live="status" class="ui-select"><option value="">All Status</option>@foreach(['draft','pending','approved','invoiced','cancelled'] as $status)<option value="{{ $status }}">{{ strtoupper($status) }}</option>@endforeach</select>
            <select wire:model.live="client_id" class="ui-select"><option value="">All Client</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select>
            <div class="flex items-end justify-end"><x-ui.action-button :href="route('finance.purchase-orders.index')" variant="ghost">Reset</x-ui.action-button></div>
        </div>
    </x-ui.form-card>

    <x-ui.table-card title="Purchase Order List" description="Review approval and invoicing progress across commercial commitments.">
        @if($purchaseOrders->count()===0)
            <div class="p-5"><x-ui.empty-state title="No purchase orders found" description="Create a PO from a confirmed booking to start the finance document chain." /></div>
        @else
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr>
                <th><button type="button" wire:click="sort('po_number')" class="ui-sort-link {{ $sortBy === 'po_number' ? 'is-active' : '' }}">PO @if($sortBy==='po_number')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('client_name')" class="ui-sort-link {{ $sortBy === 'client_name' ? 'is-active' : '' }}">Client @if($sortBy==='client_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('status')" class="ui-sort-link {{ $sortBy === 'status' ? 'is-active' : '' }}">Status @if($sortBy==='status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('total')" class="ui-sort-link {{ $sortBy === 'total' ? 'is-active' : '' }}">Total @if($sortBy==='total')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
            </tr></thead><tbody>@foreach($purchaseOrders as $po)<tr><td><a class="ui-link font-semibold text-slate-900" href="{{ route('finance.purchase-orders.show',$po) }}">{{ $po->po_number }}</a></td><td>@if($po->client)<a class="ui-link" href="{{ route('crm.clients.show', $po->client) }}">{{ $po->client->name }}</a>@else - @endif</td><td><a href="{{ route('finance.purchase-orders.index', ['status' => $po->status]) }}"><x-ui.status-badge :status="$po->status" /></a></td><td>{{ number_format($po->total,2) }}</td></tr>@endforeach</tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $purchaseOrders->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
