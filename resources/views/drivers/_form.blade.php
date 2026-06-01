@csrf
@if($isEdit)
    @method('PUT')
@endif

<div class="grid gap-4 md:grid-cols-2">
    <div><label class="mb-1 block text-sm">Pool</label><select name="pool_id" class="w-full rounded-md border-slate-300"><option value="">-- Select Pool --</option>@foreach($pools as $pool)<option value="{{ $pool->id }}" @selected(old('pool_id', $driver->pool_id)==$pool->id)>{{ $pool->name }}</option>@endforeach</select>@error('pool_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Employee Code</label><input type="text" name="employee_code" value="{{ old('employee_code', $driver->employee_code) }}" class="w-full rounded-md border-slate-300">@error('employee_code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Name *</label><input type="text" name="name" value="{{ old('name', $driver->name) }}" class="w-full rounded-md border-slate-300">@error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Phone</label><input type="text" name="phone" value="{{ old('phone', $driver->phone) }}" class="w-full rounded-md border-slate-300">@error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Email</label><input type="email" name="email" value="{{ old('email', $driver->email) }}" class="w-full rounded-md border-slate-300">@error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Status *</label><select name="status" class="w-full rounded-md border-slate-300">@foreach(['active','inactive','sick','on_leave'] as $status)<option value="{{ $status }}" @selected(old('status', $driver->status ?: 'active')===$status)>{{ strtoupper($status) }}</option>@endforeach</select>@error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">License Type</label><input type="text" name="license_type" value="{{ old('license_type', $driver->license_type) }}" class="w-full rounded-md border-slate-300"></div>
    <div><label class="mb-1 block text-sm">License Number</label><input type="text" name="license_number" value="{{ old('license_number', $driver->license_number) }}" class="w-full rounded-md border-slate-300"></div>
    <div><label class="mb-1 block text-sm">License Expired At</label><input type="date" name="license_expired_at" value="{{ old('license_expired_at', optional($driver->license_expired_at)->format('Y-m-d')) }}" class="w-full rounded-md border-slate-300"></div>
    <div class="md:col-span-2"><label class="mb-1 block text-sm">Notes</label><textarea name="notes" rows="3" class="w-full rounded-md border-slate-300">{{ old('notes', $driver->notes) }}</textarea></div>
</div>
<div class="mt-4 flex gap-2"><button class="rounded-md bg-slate-900 px-4 py-2 text-sm text-white">Save</button><a href="{{ route('drivers.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Cancel</a></div>
