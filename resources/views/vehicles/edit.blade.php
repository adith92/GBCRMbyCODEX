<x-layouts.app :title="'Edit Vehicle'" :header="'Edit Vehicle'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Fleet', 'url' => route('fleet.index')],
        ['label' => 'Vehicles', 'url' => route('fleet.vehicles.index')],
        ['label' => $vehicle->plate_number, 'url' => route('fleet.vehicles.show', $vehicle)],
        ['label' => 'Edit Vehicle'],
    ]" />

    <x-ui.page-header :title="'Edit '.$vehicle->plate_number" eyebrow="Fleet" description="Refine fleet metadata while preserving operational history and drill-down access." />

    <x-ui.form-card title="Vehicle form" description="Update status, pool, and fleet identity details for this unit.">
        <form method="POST" action="{{ route('fleet.vehicles.update', $vehicle) }}">
            @include('vehicles._form', ['isEdit' => true])
        </form>
    </x-ui.form-card>
</x-layouts.app>
