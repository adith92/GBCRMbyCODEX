@csrf
@if($isEdit)
    @method('PUT')
@endif

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="ui-label">Pool</label>
        <select name="pool_id" class="ui-select">
            <option value="">-- Select Pool --</option>
            @foreach($pools as $pool)
                <option value="{{ $pool->id }}" @selected(old('pool_id', $vehicle->pool_id) == $pool->id)>{{ $pool->name }}</option>
            @endforeach
        </select>
        @error('pool_id')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Plate Number *</label>
        <input type="text" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" class="ui-input">
        @error('plate_number')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Product Line *</label>
        <select name="product_line" class="ui-select">
            @foreach(['goldenbird','bigbird','cititrans','regular'] as $line)
                <option value="{{ $line }}" @selected(old('product_line', $vehicle->product_line ?: 'regular') === $line)>{{ strtoupper($line) }}</option>
            @endforeach
        </select>
        @error('product_line')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Status *</label>
        <select name="status" class="ui-select">
            @foreach(['available','po','maintenance','hold'] as $status)
                <option value="{{ $status }}" @selected(old('status', $vehicle->status ?: 'available') === $status)>{{ strtoupper($status) }}</option>
            @endforeach
        </select>
        @error('status')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div><label class="ui-label">Brand</label><input type="text" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="ui-input">@error('brand')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Model</label><input type="text" name="model" value="{{ old('model', $vehicle->model) }}" class="ui-input">@error('model')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Year</label><input type="number" name="year" value="{{ old('year', $vehicle->year) }}" class="ui-input">@error('year')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Seat Capacity</label><input type="number" name="seat_capacity" value="{{ old('seat_capacity', $vehicle->seat_capacity) }}" class="ui-input">@error('seat_capacity')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Odometer</label><input type="number" name="odometer" value="{{ old('odometer', $vehicle->odometer) }}" class="ui-input">@error('odometer')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div class="md:col-span-2"><label class="ui-label">Notes</label><textarea name="notes" rows="3" class="ui-textarea">{{ old('notes', $vehicle->notes) }}</textarea>@error('notes')<p class="ui-error">{{ $message }}</p>@enderror</div>
</div>
<div class="mt-5 flex flex-wrap gap-3">
    <x-ui.action-button type="submit" variant="primary">Save Vehicle</x-ui.action-button>
    <x-ui.action-button :href="$isEdit ? route('fleet.vehicles.show', $vehicle) : route('fleet.vehicles.index')" variant="secondary">Cancel</x-ui.action-button>
</div>
