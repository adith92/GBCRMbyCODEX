<x-layouts.app :title="'Create E-Voucher'" :header="'Finance / E-Vouchers / Create'">
    <section class="rounded-lg border bg-white p-4">
        <form wire:submit="save" class="grid gap-3 md:grid-cols-2">
            <div><label class="text-xs text-slate-500">Client (optional)</label><select wire:model="client_id" class="mt-1 w-full rounded border-slate-300 text-sm"><option value="">General</option>@foreach($clients as $client)<option value="{{ $client->id }}">{{ $client->name }}</option>@endforeach</select></div>
            <div><label class="text-xs text-slate-500">Status</label><select wire:model="status" class="mt-1 w-full rounded border-slate-300 text-sm"><option value="active">ACTIVE</option><option value="used">USED</option><option value="expired">EXPIRED</option><option value="cancelled">CANCELLED</option></select></div>
            <div><label class="text-xs text-slate-500">Amount</label><input wire:model="amount" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm"></div>
            <div><label class="text-xs text-slate-500">Used Amount</label><input wire:model="used_amount" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm"></div>
            <div class="md:col-span-2"><label class="text-xs text-slate-500">Expired At</label><input wire:model="expired_at" type="date" class="mt-1 w-full rounded border-slate-300 text-sm"></div>
            <div class="md:col-span-2 text-right"><button class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Save E-Voucher</button></div>
        </form>
    </section>
</x-layouts.app>
