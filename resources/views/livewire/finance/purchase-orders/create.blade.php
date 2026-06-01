<x-layouts.app :title="'Create Purchase Order'" :header="'Finance / Purchase Orders / Create'">
    @if($errorMessage)<div class="rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errorMessage }}</div>@endif
    <section class="rounded-lg border bg-white p-4">
        <form wire:submit="save" class="grid gap-3 md:grid-cols-2">
            <div class="md:col-span-2"><label class="text-xs text-slate-500">Confirmed Booking</label><select wire:model="booking_id" class="mt-1 w-full rounded border-slate-300 text-sm"><option value="">Select confirmed booking</option>@foreach($bookings as $booking)<option value="{{ $booking->id }}">{{ $booking->booking_number }} - {{ $booking->client?->name }}</option>@endforeach</select>@error('booking_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="text-xs text-slate-500">Subtotal</label><input wire:model="subtotal" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm">@error('subtotal')<p class="text-xs text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="text-xs text-slate-500">Tax</label><input wire:model="tax" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm">@error('tax')<p class="text-xs text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="text-xs text-slate-500">Total</label><input wire:model="total" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm" readonly></div>
            <div><label class="text-xs text-slate-500">Status</label><select wire:model="status" class="mt-1 w-full rounded border-slate-300 text-sm"><option value="draft">DRAFT</option><option value="pending">PENDING</option></select></div>
            <div class="md:col-span-2 text-right"><button class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Save PO</button></div>
        </form>
    </section>
</x-layouts.app>
