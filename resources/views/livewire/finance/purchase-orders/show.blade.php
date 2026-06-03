@php($idr = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.'))
<x-layouts.app :title="'Purchase Order Detail'" :header="'Finance / Purchase Orders / Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Finance', 'url' => route('finance.index')],
        ['label' => 'Purchase Orders', 'url' => route('finance.purchase-orders.index')],
        ['label' => $purchaseOrder->po_number, 'url' => route('finance.purchase-orders.show', $purchaseOrder)],
    ]" />

    <x-ui.page-header :title="$purchaseOrder->po_number" eyebrow="Purchase Order Detail" description="Review approval pulse, booking context, dan kesiapan invoice dari satu halaman yang lebih visual.">
        <x-slot:actions>
            <x-ui.status-badge :status="$purchaseOrder->status" />
            <x-back-link :fallback="route('finance.purchase-orders.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    @if($errorMessage)<div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ $errorMessage }}</div>@endif

    <section class="grid gap-3 md:grid-cols-4">
        <x-ui.stat-card label="Total PO" :value="$idr($purchaseOrder->total)" hint="💳 nilai komersial" tone="blue" />
        <x-ui.stat-card label="Client" :value="$purchaseOrder->client?->name ?? '-'" hint="👥 owner komersial" tone="emerald" />
        <x-ui.stat-card label="Invoice Linked" :value="$purchaseOrder->invoices->count()" hint="🧾 generated invoices" tone="amber" />
        <x-ui.stat-card label="Approval" :value="$purchaseOrder->approved_at ? 'Done' : 'Pending'" hint="✅ workflow approval" tone="slate" />
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
        <x-ui.form-card title="Purchase order snapshot" description="Commercial values, linked booking, dan metadata approval.">
            <dl class="ui-meta-grid">
                <div class="ui-meta-item"><dt>PO Number</dt><dd>{{ $purchaseOrder->po_number }}</dd></div>
                <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$purchaseOrder->status" /></dd></div>
                <div class="ui-meta-item"><dt>Booking</dt><dd>@if($purchaseOrder->booking)<a href="{{ route('bookings.show', $purchaseOrder->booking) }}" class="ui-link">{{ $purchaseOrder->booking->booking_number }}</a>@else - @endif</dd></div>
                <div class="ui-meta-item"><dt>Client</dt><dd>@if($purchaseOrder->client)<a href="{{ route('crm.clients.show', $purchaseOrder->client) }}" class="ui-link">{{ $purchaseOrder->client->name }}</a>@else - @endif</dd></div>
                <div class="ui-meta-item"><dt>Subtotal</dt><dd>{{ $idr($purchaseOrder->subtotal) }}</dd></div>
                <div class="ui-meta-item"><dt>Tax</dt><dd>{{ $idr($purchaseOrder->tax) }}</dd></div>
                <div class="ui-meta-item"><dt>Total</dt><dd>{{ $idr($purchaseOrder->total) }}</dd></div>
                <div class="ui-meta-item"><dt>Approved By</dt><dd>{{ $purchaseOrder->approvedBy?->name ?? '-' }}</dd></div>
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

        <x-ui.table-card title="Approval timeline" description="Visual singkat perjalanan approval PO sampai menjadi invoice.">
            <div class="ui-timeline space-y-4 p-4">
                <div class="ui-timeline-item rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Step 1</p>
                    <p class="mt-1 font-semibold text-[#042C53]">PO dibuat</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $purchaseOrder->created_at?->format('Y-m-d H:i') }} · Draft komersial siap direview.</p>
                </div>
                <div class="ui-timeline-item rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Step 2</p>
                    <p class="mt-1 font-semibold text-[#042C53]">Approval finance</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $purchaseOrder->approved_at ? $purchaseOrder->approved_at->format('Y-m-d H:i').' oleh '.$purchaseOrder->approvedBy?->name : 'Masih menunggu approval finance.' }}</p>
                </div>
                <div class="ui-timeline-item rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Step 3</p>
                    <p class="mt-1 font-semibold text-[#042C53]">Invoice conversion</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $purchaseOrder->invoices->isNotEmpty() ? 'Invoice sudah dibuat: '.$purchaseOrder->invoices->pluck('invoice_number')->join(', ') : 'Belum ada invoice, lanjutkan dari panel generate invoice.' }}</p>
                </div>
            </div>
        </x-ui.table-card>
    </section>

    <x-ui.form-card title="Generate invoice" description="Konversi approved PO menjadi invoice tanpa memutus alur finance flow.">
        <form wire:submit="createInvoice" class="grid gap-4 md:grid-cols-3">
            <select wire:model="invoice_action" class="ui-select"><option value="draft">Create as Draft</option><option value="sent">Create and Mark Sent</option></select>
            <input wire:model="due_at" type="date" class="ui-input">
            <div class="text-right">
                @if(auth()->user()->can('invoices.create'))
                    <x-ui.action-button type="submit" variant="primary">Create Invoice</x-ui.action-button>
                @else
                    <x-ui.action-button type="button" variant="secondary">Create Invoice</x-ui.action-button>
                @endif
            </div>
        </form>
        @if($purchaseOrder->invoices->isNotEmpty())
            <div class="mt-5 space-y-3 text-sm">
                <p class="font-medium text-slate-900">Existing Invoices</p>
                @foreach($purchaseOrder->invoices as $invoice)
                    <a href="{{ route('finance.invoices.show',$invoice) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-emerald-200 hover:bg-emerald-50/40">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $invoice->invoice_number }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $idr($invoice->total) }}</p>
                            </div>
                            <x-ui.status-badge :status="$invoice->status" />
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-ui.form-card>
</x-layouts.app>
