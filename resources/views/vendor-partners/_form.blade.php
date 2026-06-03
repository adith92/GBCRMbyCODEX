<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="ui-label">Code</label>
        <input name="code" value="{{ old('code', $vendor->code) }}" class="ui-input" placeholder="VP-202606-0001">
        @error('code')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Nama</label>
        <input name="name" value="{{ old('name', $vendor->name) }}" class="ui-input" required>
        @error('name')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Kategori</label>
        <select name="category" class="ui-select">
            @foreach (['vendor' => 'Vendor', 'partner' => 'Partner', 'supplier' => 'Supplier'] as $value => $label)
                <option value="{{ $value }}" @selected(old('category', $vendor->category ?: 'vendor') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Service Type</label>
        <input name="service_type" value="{{ old('service_type', $vendor->service_type) }}" class="ui-input" placeholder="Workshop, Rental, Outsource Driver">
        @error('service_type')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Contact Person</label>
        <input name="contact_person" value="{{ old('contact_person', $vendor->contact_person) }}" class="ui-input">
        @error('contact_person')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Phone</label>
        <input name="phone" value="{{ old('phone', $vendor->phone) }}" class="ui-input">
        @error('phone')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Email</label>
        <input name="email" value="{{ old('email', $vendor->email) }}" class="ui-input">
        @error('email')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">City</label>
        <input name="city" value="{{ old('city', $vendor->city) }}" class="ui-input">
        @error('city')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="ui-label">Status</label>
        <select name="status" class="ui-select">
            @foreach (['active' => 'Active', 'onboarding' => 'Onboarding', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $vendor->status ?: 'active') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="ui-label">Notes</label>
        <textarea name="notes" class="ui-textarea">{{ old('notes', $vendor->notes) }}</textarea>
        @error('notes')<p class="ui-error">{{ $message }}</p>@enderror
    </div>
</div>
