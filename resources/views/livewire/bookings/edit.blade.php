<x-layouts.app :title="'Edit Booking'" :header="'Bookings / Edit'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Bookings', 'url' => route('bookings.index')],
        ['label' => $booking->booking_number, 'url' => route('bookings.show', $booking)],
        ['label' => 'Edit Booking'],
    ]" />

    <x-ui.page-header :title="'Edit '.$booking->booking_number" eyebrow="Bookings" description="Update commercial request data while keeping assignment and finance links intact." />

    <x-ui.form-card title="Booking form" description="Adjust request timing and route details before the dispatch team takes action.">
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
                <label class="ui-label">Start Datetime</label>
                <input wire:model="start_datetime" type="datetime-local" class="ui-input">
                @error('start_datetime') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="ui-label">End Datetime</label>
                <input wire:model="end_datetime" type="datetime-local" class="ui-input">
                @error('end_datetime') <p class="ui-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="ui-label">Pickup Location</label>
                <textarea wire:model="pickup_location" rows="2" class="ui-textarea"></textarea>
            </div>
            <div>
                <label class="ui-label">Destination</label>
                <textarea wire:model="destination" rows="2" class="ui-textarea"></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="ui-label">Notes</label>
                <textarea wire:model="notes" rows="3" class="ui-textarea"></textarea>
            </div>
            <div class="md:col-span-2 flex flex-wrap justify-end gap-3">
                <x-ui.action-button :href="route('bookings.show', $booking)" variant="secondary">Cancel</x-ui.action-button>
                <x-ui.action-button type="submit" variant="primary">Update Booking</x-ui.action-button>
            </div>
        </form>
    </x-ui.form-card>
</x-layouts.app>
