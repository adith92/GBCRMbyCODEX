<x-layouts.app :title="'Create Booking'" :header="'Bookings / Create'">
    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <form wire:submit="save" class="grid gap-3 md:grid-cols-2">
            <div>
                <label class="text-xs text-slate-500">Client</label>
                <select wire:model="client_id" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                    <option value="">Select client</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
                @error('client_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-slate-500">Pool (optional)</label>
                <select wire:model="pool_id" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                    <option value="">Select pool</option>
                    @foreach ($pools as $pool)
                        <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                    @endforeach
                </select>
                @error('pool_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-slate-500">Start Datetime</label>
                <input wire:model="start_datetime" type="datetime-local" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                @error('start_datetime') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-slate-500">End Datetime</label>
                <input wire:model="end_datetime" type="datetime-local" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                @error('end_datetime') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-slate-500">Pickup Location</label>
                <textarea wire:model="pickup_location" rows="2" class="mt-1 w-full rounded-md border-slate-300 text-sm"></textarea>
                @error('pickup_location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-slate-500">Destination</label>
                <textarea wire:model="destination" rows="2" class="mt-1 w-full rounded-md border-slate-300 text-sm"></textarea>
                @error('destination') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="text-xs text-slate-500">Notes</label>
                <textarea wire:model="notes" rows="3" class="mt-1 w-full rounded-md border-slate-300 text-sm"></textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2 flex justify-end gap-2">
                <a href="{{ route('bookings.index') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Cancel</a>
                <button class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">Save Booking</button>
            </div>
        </form>
    </section>
</x-layouts.app>
