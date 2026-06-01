<x-layouts.app :title="'Purchase Orders'" :header="'Finance / Purchase Orders'">
    <section class="rounded-lg border bg-white p-4">
        <div class="grid gap-3 md:grid-cols-4">
            <input wire:model.live.debounce.300ms="search" placeholder="Search PO number" class="rounded border-slate-300 text-sm">
            <select wire:model.live="status" class="rounded border-slate-300 text-sm"><option value="">All Status</option>@foreach(['draft','pending','approved','invoiced','cancelled'] as $status)<option value="{{ $status }}">{{ strtoupper($status) }}</option>@endforeach</select>
            <select wire:model.live="client_id" class="rounded border-slate-300 text-sm"><option value="">All Client</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select>
            <div class="text-right">@can('purchase-orders.create')<a href="{{ route('finance.purchase-orders.create') }}" class="rounded bg-slate-900 px-3 py-2 text-sm text-white">+ PO</a>@endcan</div>
        </div>
    </section>
    <section class="rounded-lg border bg-white">
        @if($purchaseOrders->count()===0)
            <p class="p-6 text-sm text-slate-500">No purchase orders found.</p>
        @else
            <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">PO</th><th class="px-4 py-3 text-left">Client</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Total</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody>@foreach($purchaseOrders as $po)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $po->po_number }}</td><td class="px-4 py-3">{{ $po->client?->name }}</td><td class="px-4 py-3 uppercase text-xs">{{ $po->status }}</td><td class="px-4 py-3">{{ number_format($po->total,2) }}</td><td class="px-4 py-3 text-right"><a class="text-blue-600" href="{{ route('finance.purchase-orders.show',$po) }}">Detail</a></td></tr>@endforeach</tbody></table></div>
            <div class="p-4">{{ $purchaseOrders->links() }}</div>
        @endif
    </section>
</x-layouts.app>
