<x-layouts.app :title="'E-Vouchers'" :header="'Finance / E-Vouchers'">
    <x-ui.page-header title="E-vouchers" eyebrow="Finance" description="Keep voucher inventory readable and ready for payment validation during the demo flow.">
        <x-slot:actions>
            @can('evouchers.create')
                <x-ui.action-button :href="route('finance.e-vouchers.create')" variant="primary">+ New E-Voucher</x-ui.action-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Filter E-Vouchers" description="Search by code or filter the list by voucher status.">
        <div class="grid gap-3 md:grid-cols-3">
            <input wire:model.live.debounce.300ms="search" placeholder="Search code" class="ui-input">
            <select wire:model.live="status" class="ui-select"><option value="">All Status</option>@foreach(['active','used','expired','cancelled'] as $status)<option value="{{ $status }}">{{ strtoupper($status) }}</option>@endforeach</select>
            <div class="flex items-end justify-end"><x-ui.action-button :href="route('finance.e-vouchers.index')" variant="ghost">Reset</x-ui.action-button></div>
        </div>
    </x-ui.form-card>

    <x-ui.table-card title="Voucher Inventory" description="Track customer-linked and general vouchers alongside their remaining usable value.">
        @if($vouchers->count()===0)
            <div class="p-5"><x-ui.empty-state title="No vouchers found" description="Create a voucher to support finance demo scenarios that use non-cash settlement." /></div>
        @else
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr><th>Code</th><th>Client</th><th>Status</th><th>Amount</th><th class="text-right">Action</th></tr></thead><tbody>@foreach($vouchers as $voucher)<tr><td><p class="font-semibold text-slate-900">{{ $voucher->code }}</p></td><td>{{ $voucher->client?->name ?? 'General' }}</td><td><x-ui.status-badge :status="$voucher->status" /></td><td>{{ number_format($voucher->amount,2) }}</td><td class="text-right"><a class="ui-link" href="{{ route('finance.e-vouchers.show',$voucher) }}">Open Detail</a></td></tr>@endforeach</tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $vouchers->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
