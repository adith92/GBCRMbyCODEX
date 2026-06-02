<x-layouts.app :title="'Invoices'" :header="'Finance / Invoices'">
    <x-ui.page-header title="Invoices" eyebrow="Finance" description="Monitor invoice status, collection risk, and customer exposure with clean drill-down access." />

    <x-ui.form-card title="Filter Invoices" description="Search by invoice number or narrow the list by invoice status.">
        <div class="grid gap-3 md:grid-cols-3">
            <input wire:model.live.debounce.300ms="search" placeholder="Search invoice number" class="ui-input">
            <select wire:model.live="status" class="ui-select"><option value="">All Status</option>@foreach(['draft','sent','partial','paid','overdue','cancelled'] as $status)<option value="{{ $status }}">{{ strtoupper($status) }}</option>@endforeach</select>
            <div class="flex items-end justify-end"><x-ui.action-button :href="route('finance.invoices.index')" variant="ghost">Reset</x-ui.action-button></div>
        </div>
    </x-ui.form-card>

    <x-ui.table-card title="Invoice List" description="Commercial documents across draft, sent, partial, paid, and overdue states.">
        @if($invoices->count()===0)
            <div class="p-5"><x-ui.empty-state title="No invoices found" description="Invoices will appear here after approved purchase orders are converted." /></div>
        @else
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr><th>Invoice</th><th>Client</th><th>Status</th><th>Total</th><th class="text-right">Action</th></tr></thead><tbody>@foreach($invoices as $invoice)<tr><td><p class="font-semibold text-slate-900">{{ $invoice->invoice_number }}</p></td><td>{{ $invoice->client?->name }}</td><td><x-ui.status-badge :status="$invoice->status" /></td><td>{{ number_format($invoice->total,2) }}</td><td class="text-right"><a class="ui-link" href="{{ route('finance.invoices.show',$invoice) }}">Open Detail</a></td></tr>@endforeach</tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $invoices->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
