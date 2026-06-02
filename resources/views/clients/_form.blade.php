@csrf
@if($isEdit)
    @method('PUT')
@endif
<div class="grid gap-4 md:grid-cols-2">
    <div><label class="ui-label">Code</label><input type="text" name="code" value="{{ old('code', $client->code) }}" class="ui-input">@error('code')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Name *</label><input type="text" name="name" value="{{ old('name', $client->name) }}" class="ui-input">@error('name')<p class="ui-error">{{ $message }}</p>@enderror</div>
    <div><label class="ui-label">Legal Name</label><input type="text" name="legal_name" value="{{ old('legal_name', $client->legal_name) }}" class="ui-input"></div>
    <div><label class="ui-label">Tier *</label><select name="tier" class="ui-select">@foreach(['bronze','silver','gold','platinum'] as $tier)<option value="{{ $tier }}" @selected(old('tier', $client->tier ?: 'bronze')===$tier)>{{ strtoupper($tier) }}</option>@endforeach</select></div>
    <div><label class="ui-label">Industry</label><input type="text" name="industry" value="{{ old('industry', $client->industry) }}" class="ui-input"></div>
    <div><label class="ui-label">Tax Number</label><input type="text" name="tax_number" value="{{ old('tax_number', $client->tax_number) }}" class="ui-input"></div>
    <div><label class="ui-label">Status *</label><select name="status" class="ui-select">@foreach(['active','inactive','prospect'] as $status)<option value="{{ $status }}" @selected(old('status', $client->status ?: 'prospect')===$status)>{{ strtoupper($status) }}</option>@endforeach</select></div>
    <div class="md:col-span-2"><label class="ui-label">Billing Address</label><textarea name="billing_address" rows="2" class="ui-textarea">{{ old('billing_address', $client->billing_address) }}</textarea></div>
    <div class="md:col-span-2"><label class="ui-label">Notes</label><textarea name="notes" rows="3" class="ui-textarea">{{ old('notes', $client->notes) }}</textarea></div>
</div>
<div class="mt-5 flex flex-wrap gap-3"><x-ui.action-button type="submit" variant="primary">Save Client</x-ui.action-button><x-ui.action-button :href="$isEdit ? route('crm.clients.show', $client) : route('crm.clients.index')" variant="secondary">Cancel</x-ui.action-button></div>
