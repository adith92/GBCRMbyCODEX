<x-layouts.app :title="'E-Vouchers'" :header="'Finance / E-Vouchers'">
    <section class="rounded-lg border bg-white p-4">
        <div class="grid gap-3 md:grid-cols-3">
            <input wire:model.live.debounce.300ms="search" placeholder="Search code" class="rounded border-slate-300 text-sm">
            <select wire:model.live="status" class="rounded border-slate-300 text-sm"><option value="">All Status</option>@foreach(['active','used','expired','cancelled'] as $status)<option value="{{ $status }}">{{ strtoupper($status) }}</option>@endforeach</select>
            <div class="text-right">@can('evouchers.create')<a href="{{ route('finance.e-vouchers.create') }}" class="rounded bg-slate-900 px-3 py-2 text-sm text-white">+ E-Voucher</a>@endcan</div>
        </div>
    </section>
    <section class="rounded-lg border bg-white">
        @if($vouchers->count()===0)
            <p class="p-6 text-sm text-slate-500">No vouchers found.</p>
        @else
            <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Code</th><th class="px-4 py-3 text-left">Client</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Amount</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody>@foreach($vouchers as $voucher)<tr class="border-t"><td class="px-4 py-3 font-medium">{{ $voucher->code }}</td><td class="px-4 py-3">{{ $voucher->client?->name ?? 'General' }}</td><td class="px-4 py-3 uppercase text-xs">{{ $voucher->status }}</td><td class="px-4 py-3">{{ number_format($voucher->amount,2) }}</td><td class="px-4 py-3 text-right"><a class="text-blue-600" href="{{ route('finance.e-vouchers.show',$voucher) }}">Detail</a></td></tr>@endforeach</tbody></table></div>
            <div class="p-4">{{ $vouchers->links() }}</div>
        @endif
    </section>
</x-layouts.app>
