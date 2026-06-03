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
            <div class="ui-table-wrap"><table class="ui-table"><thead><tr>
                <th><button type="button" wire:click="sort('code')" class="ui-sort-link {{ $sortBy === 'code' ? 'is-active' : '' }}">Code @if($sortBy==='code')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('client_name')" class="ui-sort-link {{ $sortBy === 'client_name' ? 'is-active' : '' }}">Client @if($sortBy==='client_name')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('status')" class="ui-sort-link {{ $sortBy === 'status' ? 'is-active' : '' }}">Status @if($sortBy==='status')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
                <th><button type="button" wire:click="sort('amount')" class="ui-sort-link {{ $sortBy === 'amount' ? 'is-active' : '' }}">Amount @if($sortBy==='amount')<span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</button></th>
            </tr></thead><tbody>@foreach($vouchers as $voucher)<tr><td><a class="ui-link font-semibold text-slate-900" href="{{ route('finance.e-vouchers.show',$voucher) }}">{{ $voucher->code }}</a></td><td>@if($voucher->client)<a class="ui-link" href="{{ route('crm.clients.show', $voucher->client) }}">{{ $voucher->client->name }}</a>@else General @endif</td><td><a href="{{ route('finance.e-vouchers.index', ['status' => $voucher->status]) }}"><x-ui.status-badge :status="$voucher->status" /></a></td><td>{{ number_format($voucher->amount,2) }}</td></tr>@endforeach</tbody></table></div>
            <div class="border-t border-slate-200/80 px-4 py-4">{{ $vouchers->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
