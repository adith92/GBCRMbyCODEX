<x-layouts.app :title="'Assign Booking'" :header="'Pool / Assign Driver & Vehicle'">
    @if ($errorMessage)
        <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errorMessage }}</div>
    @endif

    <section class="rounded-lg border border-slate-200 bg-white p-4 text-sm">
        <h3 class="text-base font-semibold">Booking Information</h3>
        <div class="mt-3 grid gap-2 md:grid-cols-2">
            <p><strong>Booking:</strong> {{ $booking->booking_number }}</p>
            <p><strong>Status:</strong> {{ strtoupper($booking->status) }}</p>
            <p><strong>Client:</strong> {{ $booking->client?->name ?? '-' }}</p>
            <p><strong>Pool:</strong> {{ $booking->pool?->name ?? '-' }}</p>
            <p><strong>Start:</strong> {{ $booking->start_datetime?->format('Y-m-d H:i') }}</p>
            <p><strong>End:</strong> {{ $booking->end_datetime?->format('Y-m-d H:i') }}</p>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <form wire:submit="save" class="grid gap-3 md:grid-cols-2">
            <div>
                <label class="text-xs text-slate-500">Vehicle</label>
                <select wire:model="vehicle_id" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                    <option value="">Select vehicle</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}</option>
                    @endforeach
                </select>
                @error('vehicle_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-slate-500">Driver</label>
                <select wire:model="driver_id" class="mt-1 w-full rounded-md border-slate-300 text-sm">
                    <option value="">Select driver</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->employee_code }})</option>
                    @endforeach
                </select>
                @error('driver_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2 flex justify-end gap-2">
                <a href="{{ route('pool.queue') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Cancel</a>
                <button class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">Assign</button>
            </div>
        </form>
    </section>
</x-layouts.app>
