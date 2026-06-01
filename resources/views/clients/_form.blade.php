@csrf
@if($isEdit)
    @method('PUT')
@endif
<div class="grid gap-4 md:grid-cols-2">
    <div><label class="mb-1 block text-sm">Code</label><input type="text" name="code" value="{{ old('code', $client->code) }}" class="w-full rounded-md border-slate-300">@error('code')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Name *</label><input type="text" name="name" value="{{ old('name', $client->name) }}" class="w-full rounded-md border-slate-300">@error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="mb-1 block text-sm">Legal Name</label><input type="text" name="legal_name" value="{{ old('legal_name', $client->legal_name) }}" class="w-full rounded-md border-slate-300"></div>
    <div><label class="mb-1 block text-sm">Tier *</label><select name="tier" class="w-full rounded-md border-slate-300">@foreach(['bronze','silver','gold','platinum'] as $tier)<option value="{{ $tier }}" @selected(old('tier', $client->tier ?: 'bronze')===$tier)>{{ strtoupper($tier) }}</option>@endforeach</select></div>
    <div><label class="mb-1 block text-sm">Industry</label><input type="text" name="industry" value="{{ old('industry', $client->industry) }}" class="w-full rounded-md border-slate-300"></div>
    <div><label class="mb-1 block text-sm">Tax Number</label><input type="text" name="tax_number" value="{{ old('tax_number', $client->tax_number) }}" class="w-full rounded-md border-slate-300"></div>
    <div><label class="mb-1 block text-sm">Status *</label><select name="status" class="w-full rounded-md border-slate-300">@foreach(['active','inactive','prospect'] as $status)<option value="{{ $status }}" @selected(old('status', $client->status ?: 'prospect')===$status)>{{ strtoupper($status) }}</option>@endforeach</select></div>
    <div class="md:col-span-2"><label class="mb-1 block text-sm">Billing Address</label><textarea name="billing_address" rows="2" class="w-full rounded-md border-slate-300">{{ old('billing_address', $client->billing_address) }}</textarea></div>
    <div class="md:col-span-2"><label class="mb-1 block text-sm">Notes</label><textarea name="notes" rows="3" class="w-full rounded-md border-slate-300">{{ old('notes', $client->notes) }}</textarea></div>
</div>
<div class="mt-4 flex gap-2"><button class="rounded-md bg-slate-900 px-4 py-2 text-sm text-white">Save</button><a href="{{ route('crm.clients.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Cancel</a></div>
