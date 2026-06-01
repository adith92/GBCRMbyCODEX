<x-layouts.app :title="'Invoices'" :header="'Finance / Invoices'">
    <section class="rounded-lg border bg-white p-4">
        <div class="grid gap-3 md:grid-cols-3">
            <input wire:model.live.debounce.300ms="search" placeholder="Search invoice number" class="rounded border-slate-300 text-sm">
            <select wire:model.live="status" class="rounded border-slate-300 text-sm"><option value="">All Status</option>@foreach(['draft','sent','partial','paid','overdue','cancelled'] as $status)<option value="{{ $status }}">{{ strtoupper($status) }}</option>@endforeach</select>
        </div>
    </section>
    <section class="rounded-lg border bg-white">
        @if($invoices->count()===0)
            <p class="p-6 text-sm text-slate-500">No invoices found.</p>
        @else
            <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Invoice</th><th class="px-4 py-3 text-left">Client</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Total</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody>@foreach($invoices as $invoice)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $invoice->invoice_number }}</td><td class="px-4 py-3">{{ $invoice->client?->name }}</td><td class="px-4 py-3 uppercase text-xs">{{ $invoice->status }}</td><td class="px-4 py-3">{{ number_format($invoice->total,2) }}</td><td class="px-4 py-3 text-right"><a class="text-blue-600" href="{{ route('finance.invoices.show',$invoice) }}">Detail</a></td></tr>@endforeach</tbody></table></div>
            <div class="p-4">{{ $invoices->links() }}</div>
        @endif
    </section>
</x-layouts.app>
