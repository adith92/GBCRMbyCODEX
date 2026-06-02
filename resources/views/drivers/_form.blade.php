@csrf
@if($isEdit)
    @method('PUT')
@endif

<div class="grid gap-4 md:grid-cols-2">
    <div><label class="ui-label">Pool</label><select name="pool_id" class="ui-select"><option value="">-- Select Pool --</option>@foreach($pools as $pool)<option value="{{ $pool->id }}" @selected(old('pool_id', $driver->pool_id)==$pool->id)>{{ $pool->name }}</option>@endforeach</select>@error('pool_id')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Employee Code</label><input type="text" name="employee_code" value="{{ old('employee_code', $driver->employee_code) }}" class="ui-input">@error('employee_code')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Name *</label><input type="text" name="name" value="{{ old('name', $driver->name) }}" class="ui-input">@error('name')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Phone</label><input type="text" name="phone" value="{{ old('phone', $driver->phone) }}" class="ui-input">@error('phone')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Email</label><input type="email" name="email" value="{{ old('email', $driver->email) }}" class="ui-input">@error('email')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Status *</label><select name="status" class="ui-select">@foreach(['active','inactive','sick','on_leave'] as $status)<option value="{{ $status }}" @selected(old('status', $driver->status ?: 'active')===$status)>{{ strtoupper($status) }}</option>@endforeach</select>@error('status')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">License Type</label><input type="text" name="license_type" value="{{ old('license_type', $driver->license_type) }}" class="ui-input"></div>
    <div><label class="ui-label">License Number</label><input type="text" name="license_number" value="{{ old('license_number', $driver->license_number) }}" class="ui-input"></div>
    <div><label class="ui-label">License Expired At</label><input type="date" name="license_expired_at" value="{{ old('license_expired_at', optional($driver->license_expired_at)->format('Y-m-d')) }}" class="ui-input"></div>
    <div class="md:col-span-2"><label class="ui-label">Notes</label><textarea name="notes" rows="3" class="ui-textarea">{{ old('notes', $driver->notes) }}</textarea></div>
</div>
<div class="mt-5 flex flex-wrap gap-3"><x-ui.action-button type="submit" variant="primary">Save Driver</x-ui.action-button><x-ui.action-button :href="$isEdit ? route('drivers.show', $driver) : route('drivers.index')" variant="secondary">Cancel</x-ui.action-button></div>
