<x-layouts.app :title="'E-Voucher Detail'" :header="'Finance / E-Vouchers / Detail'">
    <section class="rounded-lg border bg-white p-4 text-sm">
        <div class="grid gap-2 md:grid-cols-2">
            <p><strong>Code:</strong> {{ $eVoucher->code }}</p>
            <p><strong>Status:</strong> <span class="uppercase text-xs">{{ $eVoucher->status }}</span></p>
            <p><strong>Client:</strong> {{ $eVoucher->client?->name ?? 'General' }}</p>
            <p><strong>Expired At:</strong> {{ $eVoucher->expired_at?->format('Y-m-d') ?? '-' }}</p>
            <p><strong>Amount:</strong> {{ number_format($eVoucher->amount,2) }}</p>
            <p><strong>Used Amount:</strong> {{ number_format($eVoucher->used_amount,2) }}</p>
            <p><strong>Remaining:</strong> {{ number_format($eVoucher->amount - $eVoucher->used_amount,2) }}</p>
        </div>
    </section>

    <section class="rounded-lg border bg-white p-4">
        <h3 class="text-sm font-semibold">Voucher Usage</h3>
        <div class="mt-3 space-y-2 text-sm">
            @forelse($eVoucher->payments as $payment)
                <a href="{{ route('finance.invoices.show', $payment->invoice) }}" class="block rounded border p-2 hover:bg-slate-50">{{ $payment->payment_number }} - {{ number_format($payment->amount,2) }}</a>
            @empty
                <p class="text-slate-500">No usage yet.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
