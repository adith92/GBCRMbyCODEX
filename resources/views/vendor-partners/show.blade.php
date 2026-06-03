<x-layouts.app :title="$vendor->name" :header="'Partners / Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Partners & Vendors', 'url' => route('partners.vendors.index')],
        ['label' => $vendor->name, 'url' => route('partners.vendors.show', $vendor)],
    ]" />

    <x-ui.page-header :title="$vendor->name" eyebrow="Partner Detail" description="Profil partner operasional untuk procurement, fleet support, dan maintenance collaboration.">
        <x-slot:actions>
            <x-ui.status-badge :status="$vendor->status" />
            @can('clients.update')
                <x-ui.action-button :href="route('partners.vendors.edit', $vendor)" variant="secondary">Edit</x-ui.action-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <section class="grid gap-3 md:grid-cols-4">
        <x-ui.stat-card label="Category" :value="ucfirst($vendor->category)" hint="Kategori partner." tone="blue" />
        <x-ui.stat-card label="Service" :value="$vendor->service_type ?: '-'" hint="Layanan utama." tone="emerald" />
        <x-ui.stat-card label="PIC" :value="$vendor->contact_person ?: '-'" hint="Kontak utama." tone="amber" />
        <x-ui.stat-card label="City" :value="$vendor->city ?: '-'" hint="Coverage area." tone="slate" />
    </section>

    <x-ui.form-card title="Partner snapshot" description="Data inti partner/vendor untuk flow procurement dan support operasional.">
        <dl class="ui-meta-grid">
            <div class="ui-meta-item"><dt>Code</dt><dd>{{ $vendor->code }}</dd></div>
            <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$vendor->status" /></dd></div>
            <div class="ui-meta-item"><dt>Email</dt><dd>{{ $vendor->email ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Phone</dt><dd>{{ $vendor->phone ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>City</dt><dd>{{ $vendor->city ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Updated</dt><dd>{{ $vendor->updated_at?->format('Y-m-d H:i') }}</dd></div>
            <div class="ui-meta-item md:col-span-2 xl:col-span-3"><dt>Notes</dt><dd>{{ $vendor->notes ?: 'Belum ada catatan partner.' }}</dd></div>
        </dl>
    </x-ui.form-card>
</x-layouts.app>
