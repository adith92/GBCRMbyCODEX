<x-layouts.app :title="'Create Vehicle'" :header="'Create Vehicle'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Fleet', 'url' => route('fleet.index')],
        ['label' => 'Vehicles', 'url' => route('fleet.vehicles.index')],
        ['label' => 'Create Vehicle'],
    ]" />

    <x-ui.page-header title="Create vehicle" eyebrow="Fleet" description="Register a fleet unit with enough data to support dispatch and maintenance workflows." />

    <x-ui.form-card title="Vehicle form" description="Operational identity and status metadata used across fleet, pool, and maintenance modules.">
        <form method="POST" action="{{ route('fleet.vehicles.store') }}">
            @include('vehicles._form', ['isEdit' => false])
        </form>
    </x-ui.form-card>
</x-layouts.app>
