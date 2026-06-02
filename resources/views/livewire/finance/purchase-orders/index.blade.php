<x-layouts.app :title="'Purchase Orders'" :header="'Finance / Purchase Orders'">
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
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr><th>PO</th><th>Client</th><th>Status</th><th>Total</th><th class="text-right">Action</th></tr></thead><tbody>@foreach($purchaseOrders as $po)<tr><td><p class="font-semibold text-slate-900">{{ $po->po_number }}</p></td><td>{{ $po->client?->name }}</td><td><x-ui.status-badge :status="$po->status" /></td><td>{{ number_format($po->total,2) }}</td><td class="text-right"><a class="ui-link" href="{{ route('finance.purchase-orders.show',$po) }}">Open Detail</a></td></tr>@endforeach</tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $purchaseOrders->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
