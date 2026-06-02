<x-layouts.app :title="'Purchase Order Detail'" :header="'Finance / Purchase Orders / Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Finance', 'url' => route('finance.index')],
        ['label' => 'Purchase Orders', 'url' => route('finance.purchase-orders.index')],
        ['label' => $purchaseOrder->po_number, 'url' => route('finance.purchase-orders.show', $purchaseOrder)],
    ]" />

    <x-ui.page-header :title="$purchaseOrder->po_number" eyebrow="Purchase Order Detail" description="Review PO approval status, booking linkage, and invoice conversion from one place.">
        <x-slot:actions>
            <x-ui.status-badge :status="$purchaseOrder->status" />
            <x-back-link :fallback="route('finance.purchase-orders.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    @if($errorMessage)<div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ $errorMessage }}</div>@endif
    <x-ui.form-card title="Purchase order snapshot" description="Commercial values, linked booking, and current approval metadata.">
        <dl class="ui-meta-grid">
            <div class="ui-meta-item"><dt>PO Number</dt><dd>{{ $purchaseOrder->po_number }}</dd></div>
            <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$purchaseOrder->status" /></dd></div>
            <div class="ui-meta-item"><dt>Booking</dt><dd>@if($purchaseOrder->booking)<a href="{{ route('bookings.show', $purchaseOrder->booking) }}" class="ui-link">{{ $purchaseOrder->booking->booking_number }}</a>@else - @endif</dd></div>
            <div class="ui-meta-item"><dt>Client</dt><dd>@if($purchaseOrder->client)<a href="{{ route('crm.clients.show', $purchaseOrder->client) }}" class="ui-link">{{ $purchaseOrder->client->name }}</a>@else - @endif</dd></div>
            <div class="ui-meta-item"><dt>Subtotal</dt><dd>{{ number_format($purchaseOrder->subtotal,2) }}</dd></div>
            <div class="ui-meta-item"><dt>Tax</dt><dd>{{ number_format($purchaseOrder->tax,2) }}</dd></div>
            <div class="ui-meta-item"><dt>Total</dt><dd>{{ number_format($purchaseOrder->total,2) }}</dd></div>
            <div class="ui-meta-item"><dt>Approved</dt><dd>{{ $purchaseOrder->approvedBy?->name ?? '-' }} {{ $purchaseOrder->approved_at?->format('Y-m-d H:i') }}</dd></div>
        </dl>
        <div class="mt-5 flex flex-wrap gap-3">
            @if(in_array($purchaseOrder->status,['draft','pending']) && auth()->user()->can('purchase-orders.create'))
                <x-ui.action-button :href="route('finance.purchase-orders.edit',$purchaseOrder)" variant="secondary">Edit</x-ui.action-button>
            @endif
            @if(in_array($purchaseOrder->status,['draft','pending']) && auth()->user()->can('purchase-orders.approve'))
                <x-ui.action-button wire:click="approve" wire:confirm="Approve this PO?" variant="success">Approve</x-ui.action-button>
            @endif
        </div>
    </x-ui.form-card>

    <x-ui.form-card title="Generate invoice" description="Convert approved PO into an invoice while keeping the finance flow connected.">
        <form wire:submit="createInvoice" class="grid gap-4 md:grid-cols-3">
            <select wire:model="invoice_action" class="ui-select"><option value="draft">Create as Draft</option><option value="sent">Create and Mark Sent</option></select>
            <input wire:model="due_at" type="date" class="ui-input">
            <div class="text-right"><x-ui.action-button type="submit" variant="primary" @disabled(!auth()->user()->can('invoices.create'))>Create Invoice</x-ui.action-button></div>
        </form>
        @if($purchaseOrder->invoices->isNotEmpty())
            <div class="mt-5 space-y-3 text-sm">
                <p class="font-medium text-slate-900">Existing Invoices</p>
                @foreach($purchaseOrder->invoices as $invoice)
                    <a href="{{ route('finance.invoices.show',$invoice) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-emerald-200 hover:bg-emerald-50/40">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-semibold text-slate-900">{{ $invoice->invoice_number }}</p>
                            <x-ui.status-badge :status="$invoice->status" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-ui.form-card>
</x-layouts.app>
