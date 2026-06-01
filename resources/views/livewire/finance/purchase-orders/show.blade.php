<x-layouts.app :title="'Purchase Order Detail'" :header="'Finance / Purchase Orders / Detail'">
    @if($errorMessage)<div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errorMessage }}</div>@endif
    <section class="rounded-lg border bg-white p-4 text-sm">
        <div class="grid gap-2 md:grid-cols-2">
            <p><strong>PO Number:</strong> {{ $purchaseOrder->po_number }}</p>
            <p><strong>Status:</strong> <span class="uppercase text-xs">{{ $purchaseOrder->status }}</span></p>
            <p><strong>Booking:</strong> {{ $purchaseOrder->booking?->booking_number }}</p>
            <p><strong>Client:</strong> {{ $purchaseOrder->client?->name }}</p>
            <p><strong>Subtotal:</strong> {{ number_format($purchaseOrder->subtotal,2) }}</p>
            <p><strong>Tax:</strong> {{ number_format($purchaseOrder->tax,2) }}</p>
            <p><strong>Total:</strong> {{ number_format($purchaseOrder->total,2) }}</p>
            <p><strong>Approved:</strong> {{ $purchaseOrder->approvedBy?->name ?? '-' }} {{ $purchaseOrder->approved_at?->format('Y-m-d H:i') }}</p>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            @if(in_array($purchaseOrder->status,['draft','pending']) && auth()->user()->can('purchase-orders.create'))
                <a href="{{ route('finance.purchase-orders.edit',$purchaseOrder) }}" class="rounded border px-3 py-2 text-sm">Edit</a>
            @endif
            @if(in_array($purchaseOrder->status,['draft','pending']) && auth()->user()->can('purchase-orders.approve'))
                <button wire:click="approve" wire:confirm="Approve this PO?" class="rounded bg-emerald-600 px-3 py-2 text-sm text-white">Approve</button>
            @endif
        </div>
    </section>

    <section class="rounded-lg border bg-white p-4">
        <h3 class="text-sm font-semibold">Generate Invoice</h3>
        <form wire:submit="createInvoice" class="mt-3 grid gap-3 md:grid-cols-3">
            <select wire:model="invoice_action" class="rounded border-slate-300 text-sm"><option value="draft">Create as Draft</option><option value="sent">Create and Mark Sent</option></select>
            <input wire:model="due_at" type="date" class="rounded border-slate-300 text-sm">
            <div class="text-right"><button class="rounded bg-slate-900 px-3 py-2 text-sm text-white" @disabled(!auth()->user()->can('invoices.create'))>Create Invoice</button></div>
        </form>
        @if($purchaseOrder->invoices->isNotEmpty())
            <div class="mt-4 text-sm">
                <p class="mb-1 font-medium">Existing Invoices</p>
                @foreach($purchaseOrder->invoices as $invoice)
                    <a href="{{ route('finance.invoices.show',$invoice) }}" class="block rounded border p-2 hover:bg-slate-50">{{ $invoice->invoice_number }} <span class="float-right uppercase text-xs">{{ $invoice->status }}</span></a>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
