<x-layouts.app :title="'Edit Purchase Order'" :header="'Finance / Purchase Orders / Edit'">
    <section class="rounded-lg border bg-white p-4">
        <form wire:submit="save" class="grid gap-3 md:grid-cols-2">
            <div><label class="text-xs text-slate-500">Subtotal</label><input wire:model="subtotal" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm"></div>
            <div><label class="text-xs text-slate-500">Tax</label><input wire:model="tax" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm"></div>
            <div><label class="text-xs text-slate-500">Total</label><input wire:model="total" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm" readonly></div>
            <div><label class="text-xs text-slate-500">Status</label><select wire:model="status" class="mt-1 w-full rounded border-slate-300 text-sm"><option value="draft">DRAFT</option><option value="pending">PENDING</option><option value="cancelled">CANCELLED</option></select></div>
            <div class="md:col-span-2 text-right"><button class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Update PO</button></div>
        </form>
    </section>
</x-layouts.app>
