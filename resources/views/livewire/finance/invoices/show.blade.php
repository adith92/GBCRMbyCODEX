<x-layouts.app :title="'Invoice Detail'" :header="'Finance / Invoices / Detail'">
    @if($errorMessage)<div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errorMessage }}</div>@endif
    <section class="rounded-lg border bg-white p-4 text-sm">
        <div class="grid gap-2 md:grid-cols-2">
            <p><strong>Invoice:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Status:</strong> <span class="uppercase text-xs">{{ $invoice->status }}</span></p>
            <p><strong>PO:</strong> {{ $invoice->purchaseOrder?->po_number }}</p>
            <p><strong>Client:</strong> {{ $invoice->client?->name }}</p>
            <p><strong>Issued At:</strong> {{ $invoice->issued_at?->format('Y-m-d') }}</p>
            <p><strong>Due At:</strong> {{ $invoice->due_at?->format('Y-m-d') }}</p>
            <p><strong>Total:</strong> {{ number_format($invoice->total,2) }}</p>
            <p><strong>Paid Amount:</strong> {{ number_format($invoice->paid_amount,2) }}</p>
        </div>
        @if($invoice->status === 'draft' && auth()->user()->can('invoices.update'))
            <div class="mt-4"><button wire:click="markSent" class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Mark as Sent</button></div>
        @endif
    </section>

    <section class="rounded-lg border bg-white p-4">
        <h3 class="text-sm font-semibold">Add Payment</h3>
        @can('payments.create')
        <form wire:submit="addPayment" class="mt-3 grid gap-3 md:grid-cols-3">
            <input wire:model="paid_at" type="date" class="rounded border-slate-300 text-sm"> 
            <input wire:model="amount" type="number" step="0.01" placeholder="Amount" class="rounded border-slate-300 text-sm">
            <select wire:model.live="method" class="rounded border-slate-300 text-sm"><option value="bank_transfer">Bank Transfer</option><option value="cash">Cash</option><option value="evoucher">E-Voucher</option><option value="other">Other</option></select>
            <input wire:model="reference_number" placeholder="Reference Number" class="rounded border-slate-300 text-sm">
            <input wire:model="notes" placeholder="Notes" class="rounded border-slate-300 text-sm">
            @if($method === 'evoucher')
                <select wire:model="e_voucher_id" class="rounded border-slate-300 text-sm"><option value="">Select e-voucher</option>@foreach($vouchers as $voucher)<option value="{{ $voucher->id }}">{{ $voucher->code }} (rem: {{ number_format($voucher->amount - $voucher->used_amount,2) }})</option>@endforeach</select>
            @endif
            <div class="md:col-span-3 text-right"><button class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Save Payment</button></div>
        </form>
        @endcan

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Payment</th><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Method</th><th class="px-4 py-3 text-left">Amount</th><th class="px-4 py-3 text-left">Voucher</th></tr></thead><tbody>@forelse($invoice->payments as $payment)<tr class="border-t"><td class="px-4 py-3">{{ $payment->payment_number }}</td><td class="px-4 py-3">{{ $payment->paid_at?->format('Y-m-d') }}</td><td class="px-4 py-3">{{ $payment->method }}</td><td class="px-4 py-3">{{ number_format($payment->amount,2) }}</td><td class="px-4 py-3">{{ $payment->eVoucher?->code ?? '-' }}</td></tr>@empty<tr><td class="px-4 py-4 text-slate-500" colspan="5">No payments yet.</td></tr>@endforelse</tbody></table>
        </div>
    </section>
</x-layouts.app>
