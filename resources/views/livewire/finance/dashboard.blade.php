<x-layouts.app :title="'Finance Dashboard'" :header="'Finance / Dashboard'">
    <x-ui.page-header title="Finance control room" eyebrow="Finance" description="Keep revenue exposure, collection performance, and overdue follow-up visible without leaving the demo flow." />

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Invoice Sent / Partial" :value="number_format($totalInvoiceOpen, 2)" hint="Open invoiced amount still under collection." tone="amber" />
        <x-ui.stat-card label="Total Paid" :value="number_format($totalPaid, 2)" hint="Collected payment value recorded in the system." tone="emerald" />
        <x-ui.stat-card label="Outstanding" :value="number_format($outstanding, 2)" hint="Remaining open balance after recorded payments." tone="slate" />
        <x-ui.stat-card label="Overdue Invoices" :value="$overdueCount" hint="Requires immediate finance follow-up." tone="rose" />
    </section>

    <section class="grid gap-5 xl:grid-cols-2">
        <x-ui.table-card title="Latest Invoices" description="Recent invoice movement for drill-down into collection activity.">
            @if($latestInvoices->isEmpty())
                <div class="p-5"><x-ui.empty-state title="No invoices yet" description="New invoices will appear here after approved purchase orders are converted." /></div>
            @else
                <div class="space-y-3 p-5 text-sm">
                    @foreach($latestInvoices as $invoice)
                        <a href="{{ route('finance.invoices.show', $invoice) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $invoice->invoice_number }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $invoice->client?->name }}</p>
                                </div>
                                <x-ui.status-badge :status="$invoice->status" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>

        <x-ui.table-card title="Latest Payments" description="Collection activity recorded by the finance team.">
            @if($latestPayments->isEmpty())
                <div class="p-5"><x-ui.empty-state title="No payments yet" description="Payment activity will show once collections are recorded from invoice detail." /></div>
            @else
                <div class="space-y-3 p-5 text-sm">
                    @foreach($latestPayments as $payment)
                        <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $payment->payment_number }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ number_format($payment->amount, 2) }}</p>
                                </div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $payment->paid_at?->format('Y-m-d') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>
    </section>
</x-layouts.app>
