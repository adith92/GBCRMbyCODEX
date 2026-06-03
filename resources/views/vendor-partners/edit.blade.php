<x-layouts.app :title="'Edit Partner'" :header="'Partners / Edit'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Partners & Vendors', 'url' => route('partners.vendors.index')],
        ['label' => $vendor->name, 'url' => route('partners.vendors.show', $vendor)],
        ['label' => 'Edit', 'url' => route('partners.vendors.edit', $vendor)],
    ]" />

    <x-ui.page-header :title="'Edit '.$vendor->name" eyebrow="Partner Ecosystem" description="Perbarui data partner supaya tim sales, operation, dan finance tetap sinkron." />

    <x-ui.form-card title="Edit partner" description="Jaga data partner tetap rapi untuk demo dan operational continuity.">
        <form method="POST" action="{{ route('partners.vendors.update', $vendor) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('vendor-partners._form')
            <div class="flex justify-end gap-2">
                <x-ui.action-button :href="route('partners.vendors.show', $vendor)" variant="ghost">Back</x-ui.action-button>
                <x-ui.action-button type="submit" variant="primary">Update Partner</x-ui.action-button>
            </div>
        </form>
    </x-ui.form-card>
</x-layouts.app>
