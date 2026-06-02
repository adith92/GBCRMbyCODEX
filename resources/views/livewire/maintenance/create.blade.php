<x-layouts.app :title="'Create Maintenance'" :header="'Maintenance / Create'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <section class="rounded-lg border bg-white p-4">
        <form wire:submit="save" class="grid gap-3 md:grid-cols-2">
            <div>
                <label class="text-xs text-slate-500">Vehicle</label>
                <select wire:model="vehicle_id" class="mt-1 w-full rounded border-slate-300 text-sm">
                    <option value="">Select vehicle</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }}</option>
                    @endforeach
                </select>
                @error('vehicle_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-xs text-slate-500">Status</label>
                <select wire:model="status" class="mt-1 w-full rounded border-slate-300 text-sm">
                    @foreach(['scheduled', 'in_progress', 'completed', 'cancelled'] as $status)
                        <option value="{{ $status }}">{{ strtoupper($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="text-xs text-slate-500">Title</label>
                <input wire:model="title" class="mt-1 w-full rounded border-slate-300 text-sm">
                @error('title')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div><label class="text-xs text-slate-500">Start At</label><input wire:model="start_at" type="datetime-local" class="mt-1 w-full rounded border-slate-300 text-sm"></div>
            <div><label class="text-xs text-slate-500">End At</label><input wire:model="end_at" type="datetime-local" class="mt-1 w-full rounded border-slate-300 text-sm"></div>
            <div><label class="text-xs text-slate-500">Cost</label><input wire:model="cost" type="number" step="0.01" class="mt-1 w-full rounded border-slate-300 text-sm"></div>
            <div class="md:col-span-2"><label class="text-xs text-slate-500">Notes</label><textarea wire:model="notes" rows="4" class="mt-1 w-full rounded border-slate-300 text-sm"></textarea></div>
            <div class="md:col-span-2 flex justify-end gap-2">
                <x-back-link :fallback="route('maintenance.index')" />
                <button class="rounded bg-slate-900 px-3 py-2 text-sm text-white">Save Maintenance</button>
            </div>
        </form>
    </section>
</x-layouts.app>
