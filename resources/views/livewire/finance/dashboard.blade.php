<x-layouts.app :title="'Finance Dashboard'" :header="'Finance / Dashboard'">
    <section class="grid gap-3 md:grid-cols-4">
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Invoice Sent/Partial</p><p class="mt-1 text-xl font-semibold">{{ number_format($totalInvoiceOpen, 2) }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Total Paid</p><p class="mt-1 text-xl font-semibold">{{ number_format($totalPaid, 2) }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Outstanding</p><p class="mt-1 text-xl font-semibold">{{ number_format($outstanding, 2) }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Overdue Invoices</p><p class="mt-1 text-xl font-semibold">{{ $overdueCount }}</p></div>
    </section>

    <section class="grid gap-4 md:grid-cols-2">
        <div class="rounded-lg border bg-white p-4">
            <h3 class="text-sm font-semibold">Latest Invoices</h3>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($latestInvoices as $invoice)
                    <a href="{{ route('finance.invoices.show', $invoice) }}" class="block rounded border p-2 hover:bg-slate-50">
                        {{ $invoice->invoice_number }} - {{ $invoice->client?->name }}
                        <span class="float-right uppercase text-xs">{{ $invoice->status }}</span>
                    </a>
                @empty
                    <p class="text-slate-500">No invoices yet.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-lg border bg-white p-4">
            <h3 class="text-sm font-semibold">Latest Payments</h3>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($latestPayments as $payment)
                    <div class="rounded border p-2">
                        {{ $payment->payment_number }} - {{ number_format($payment->amount, 2) }}
                        <span class="float-right text-xs">{{ $payment->paid_at?->format('Y-m-d') }}</span>
                    </div>
                @empty
                    <p class="text-slate-500">No payments yet.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.app>
