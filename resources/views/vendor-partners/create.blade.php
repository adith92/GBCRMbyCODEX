<x-layouts.app :title="'Tambah Partner'" :header="'Partners / Create'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Partners & Vendors', 'url' => route('partners.vendors.index')],
        ['label' => 'Create', 'url' => route('partners.vendors.create')],
    ]" />

    <x-ui.page-header title="Tambah Partner / Vendor" eyebrow="Partner Ecosystem" description="Buat mitra operasional baru untuk workshop, supplier, rental, atau vendor support." />

    <x-ui.form-card title="Partner form" description="Simpan data partner untuk dipakai dalam flow operasional dan procurement demo.">
        <form method="POST" action="{{ route('partners.vendors.store') }}" class="space-y-4">
            @csrf
            @include('vendor-partners._form')
            <div class="flex justify-end gap-2">
                <x-ui.action-button :href="route('partners.vendors.index')" variant="ghost">Cancel</x-ui.action-button>
                <x-ui.action-button type="submit" variant="primary">Save Partner</x-ui.action-button>
            </div>
        </form>
    </x-ui.form-card>
</x-layouts.app>
