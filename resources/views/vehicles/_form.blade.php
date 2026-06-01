@csrf
@if($isEdit)
    @method('PUT')
@endif

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm">Pool</label>
        <select name="pool_id" class="w-full rounded-md border-slate-300">
            <option value="">-- Select Pool --</option>
            @foreach($pools as $pool)
                <option value="{{ $pool->id }}" @selected(old('pool_id', $vehicle->pool_id) == $pool->id)>{{ $pool->name }}</option>
            @endforeach
        </select>
        @error('pool_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm">Plate Number *</label>
        <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" class="w-full rounded-md border-slate-300">
        @error('plate_number')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm">Product Line *</label>
        <select name="product_line" class="w-full rounded-md border-slate-300">
            @foreach(['goldenbird','bigbird','cititrans','regular'] as $line)
                <option value="{{ $line }}" @selected(old('product_line', $vehicle->product_line ?: 'regular') === $line)>{{ strtoupper($line) }}</option>
            @endforeach
        </select>
        @error('product_line')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm">Status *</label>
        <select name="status" class="w-full rounded-md border-slate-300">
            @foreach(['available','po','maintenance','hold'] as $status)
                <option value="{{ $status }}" @selected(old('status', $vehicle->status ?: 'available') === $status)>{{ strtoupper($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <div><label class="mb-1 block text-sm">Brand</label><input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="w-full rounded-md border-slate-300">@error('brand')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Model</label><input type="text" name="model" value="{{ old('model', $vehicle->model) }}" class="w-full rounded-md border-slate-300">@error('model')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Year</label><input type="number" name="year" value="{{ old('year', $vehicle->year) }}" class="w-full rounded-md border-slate-300">@error('year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Seat Capacity</label><input type="number" name="seat_capacity" value="{{ old('seat_capacity', $vehicle->seat_capacity) }}" class="w-full rounded-md border-slate-300">@error('seat_capacity')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Odometer</label><input type="number" name="odometer" value="{{ old('odometer', $vehicle->odometer) }}" class="w-full rounded-md border-slate-300">@error('odometer')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div class="md:col-span-2"><label class="mb-1 block text-sm">Notes</label><textarea name="notes" rows="3" class="w-full rounded-md border-slate-300">{{ old('notes', $vehicle->notes) }}</textarea>@error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
</div>
<div class="mt-4 flex gap-2">
    <button class="rounded-md bg-slate-900 px-4 py-2 text-sm text-white">Save</button>
    <a href="{{ route('fleet.vehicles.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Cancel</a>
</div>
