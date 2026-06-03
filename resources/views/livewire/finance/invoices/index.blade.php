<x-layouts.app :title="'Invoices'" :header="'Finance / Invoices'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Finance', 'url' => route('finance.index')],
        ['label' => 'Invoices', 'url' => route('finance.invoices.index')],
    ]" />

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
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr>
                <th><button type="button" wire:click="sort('invoice_number')" class="ui-sort-link {{ $sortBy === 'invoice_number' ? 'is-active' : '' }}">Invoice @if($sortBy==='invoice_number')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('client_name')" class="ui-sort-link {{ $sortBy === 'client_name' ? 'is-active' : '' }}">Client @if($sortBy==='client_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('status')" class="ui-sort-link {{ $sortBy === 'status' ? 'is-active' : '' }}">Status @if($sortBy==='status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('total')" class="ui-sort-link {{ $sortBy === 'total' ? 'is-active' : '' }}">Total @if($sortBy==='total')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
            </tr></thead><tbody>@foreach($invoices as $invoice)<tr><td><a class="ui-link font-semibold text-slate-900" href="{{ route('finance.invoices.show',$invoice) }}">{{ $invoice->invoice_number }}</a></td><td>@if($invoice->client)<a class="ui-link" href="{{ route('crm.clients.show', $invoice->client) }}">{{ $invoice->client->name }}</a>@else - @endif</td><td><a href="{{ route('finance.invoices.index', ['status' => $invoice->status]) }}"><x-ui.status-badge :status="$invoice->status" /></a></td><td>{{ number_format($invoice->total,2) }}</td></tr>@endforeach</tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $invoices->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
