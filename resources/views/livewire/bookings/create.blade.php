<x-layouts.app :title="'Create Booking'" :header="'Bookings / Create'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Bookings', 'url' => route('bookings.index')],
        ['label' => 'Create Booking'],
    ]" />

    <x-ui.page-header title="Create booking" eyebrow="Bookings" description="Capture a client transport request without breaking the later dispatch and finance workflow." />

    <x-ui.form-card title="Booking request form" description="Fill the commercial details first. Pool, driver, and vehicle can still be completed later by dispatch.">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="ui-label">Client</label>
                <select wire:model="client_id" class="ui-select">
                    <option value="">Select client</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
                @error('client_id') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="ui-label">Pool (optional)</label>
                <select wire:model="pool_id" class="ui-select">
                    <option value="">Select pool</option>
                    @foreach ($pools as $pool)
                        <option value="{{ $pool->id }}">{{ $pool->name }}</option>
                    @endforeach
                </select>
                @error('pool_id') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="ui-label">Start datetime</label>
                <input wire:model="start_datetime" type="datetime-local" class="ui-input">
                @error('start_datetime') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="ui-label">End datetime</label>
                <input wire:model="end_datetime" type="datetime-local" class="ui-input">
                @error('end_datetime') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="ui-label">Pickup location</label>
                <textarea wire:model="pickup_location" rows="3" class="ui-textarea"></textarea>
                @error('pickup_location') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="ui-label">Destination</label>
                <textarea wire:model="destination" rows="3" class="ui-textarea"></textarea>
                @error('destination') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="ui-label">Notes</label>
                <textarea wire:model="notes" rows="4" class="ui-textarea"></textarea>
                @error('notes') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2 flex flex-wrap justify-end gap-3">
                <x-ui.action-button :href="route('bookings.index')" variant="secondary">Cancel</x-ui.action-button>
                <x-ui.action-button type="submit" variant="primary">Save Booking</x-ui.action-button>
            </div>
        </form>
    </x-ui.form-card>
</x-layouts.app>
