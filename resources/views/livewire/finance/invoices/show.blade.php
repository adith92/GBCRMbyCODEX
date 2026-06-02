<x-layouts.app :title="'Invoice Detail'" :header="'Finance / Invoices / Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Finance', 'url' => route('finance.index')],
        ['label' => 'Invoices', 'url' => route('finance.invoices.index')],
        ['label' => $invoice->invoice_number, 'url' => route('finance.invoices.show', $invoice)],
    ]" />

    <x-ui.page-header :title="$invoice->invoice_number" eyebrow="Invoice Detail" description="Review invoice metadata, linked PO and booking context, then record customer payments safely.">
        <x-slot:actions>
            <x-ui.status-badge :status="$invoice->status" />
            <x-back-link :fallback="route('finance.invoices.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    @if($errorMessage)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ $errorMessage }}</div>
    @endif

    <x-ui.form-card title="Invoice snapshot" description="Commercial and finance metadata for this invoice.">
        <dl class="ui-meta-grid">
            <div class="ui-meta-item"><dt>Invoice</dt><dd>{{ $invoice->invoice_number }}</dd></div>
            <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$invoice->status" /></dd></div>
            <div class="ui-meta-item"><dt>PO</dt><dd>@if($invoice->purchaseOrder)<a href="{{ route('finance.purchase-orders.show', $invoice->purchaseOrder) }}" class="ui-link">{{ $invoice->purchaseOrder->po_number }}</a>@else - @endif</dd></div>
            <div class="ui-meta-item"><dt>Client</dt><dd>@if($invoice->client)<a href="{{ route('crm.clients.show', $invoice->client) }}" class="ui-link">{{ $invoice->client->name }}</a>@else - @endif</dd></div>
            <div class="ui-meta-item"><dt>Issued At</dt><dd>{{ $invoice->issued_at?->format('Y-m-d') }}</dd></div>
            <div class="ui-meta-item"><dt>Due At</dt><dd>{{ $invoice->due_at?->format('Y-m-d') }}</dd></div>
            <div class="ui-meta-item"><dt>Total</dt><dd>{{ number_format($invoice->total,2) }}</dd></div>
            <div class="ui-meta-item"><dt>Paid Amount</dt><dd>{{ number_format($invoice->paid_amount,2) }}</dd></div>
            @if($invoice->purchaseOrder?->booking)
                <div class="ui-meta-item md:col-span-2 xl:col-span-3"><dt>Booking</dt><dd><a href="{{ route('bookings.show', $invoice->purchaseOrder->booking) }}" class="ui-link">{{ $invoice->purchaseOrder->booking->booking_number }}</a></dd></div>
            @endif
        </dl>
        @if($invoice->status === 'draft' && auth()->user()->can('invoices.update'))
            <div class="mt-5"><x-ui.action-button wire:click="markSent" variant="primary">Mark as Sent</x-ui.action-button></div>
        @endif
    </x-ui.form-card>

    <x-ui.form-card title="Add Payment" description="Record collection safely, including optional E-voucher usage.">
        @can('payments.create')
        <form wire:submit="addPayment" class="grid gap-4 md:grid-cols-3">
            <div>
                <label class="ui-label">Paid at</label>
                <input wire:model="paid_at" type="date" class="ui-input">
            </div>
            <div>
                <label class="ui-label">Amount</label>
                <input wire:model="amount" type="number" step="0.01" placeholder="Amount" class="ui-input">
            </div>
            <div>
                <label class="ui-label">Method</label>
                <select wire:model.live="method" class="ui-select"><option value="bank_transfer">Bank Transfer</option><option value="cash">Cash</option><option value="evoucher">E-Voucher</option><option value="other">Other</option></select>
            </div>
            <div>
                <label class="ui-label">Reference Number</label>
                <input wire:model="reference_number" placeholder="Reference Number" class="ui-input">
            </div>
            <div>
                <label class="ui-label">Notes</label>
                <input wire:model="notes" placeholder="Notes" class="ui-input">
            </div>
            @if($method === 'evoucher')
                <div>
                    <label class="ui-label">E-Voucher</label>
                    <select wire:model="e_voucher_id" class="ui-select"><option value="">Select e-voucher</option>@foreach($vouchers as $voucher)<option value="{{ $voucher->id }}">{{ $voucher->code }} (rem: {{ number_format($voucher->amount - $voucher->used_amount,2) }})</option>@endforeach</select>
                </div>
            @endif
            <div class="md:col-span-3 flex justify-end"><x-ui.action-button type="submit" variant="primary">Save Payment</x-ui.action-button></div>
        </form>
        @endcan
    </x-ui.form-card>

    <x-ui.table-card title="Payment History" description="Recorded payments against this invoice.">
        <div class="ui-table-wrap">
            <table class="ui-table"><thead><tr><th>Payment</th><th>Date</th><th>Method</th><th>Amount</th><th>Voucher</th></tr></thead><tbody>@forelse($invoice->payments as $payment)<tr><td>{{ $payment->payment_number }}</td><td>{{ $payment->paid_at?->format('Y-m-d') }}</td><td>{{ $payment->method }}</td><td>{{ number_format($payment->amount,2) }}</td><td>{{ $payment->eVoucher?->code ?? '-' }}</td></tr>@empty<tr><td colspan="5" class="px-4 py-6"><x-ui.empty-state title="No payments yet" description="Payments recorded from this screen will appear here instantly." /></td></tr>@endforelse</tbody></table>
        </div>
    </x-ui.table-card>
</x-layouts.app>
