<x-layouts.app :title="'Edit Driver'" :header="'Edit Driver'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Drivers', 'url' => route('drivers.index')],
        ['label' => $driver->name, 'url' => route('drivers.show', $driver)],
        ['label' => 'Edit Driver'],
    ]" />

    <x-ui.page-header :title="'Edit '.$driver->name" eyebrow="Drivers" description="Update driver readiness and profile data without losing booking context." />

    <x-ui.form-card title="Driver form" description="Refine operational status, license data, and contact information for this driver.">
        <form method="POST" action="{{ route('drivers.update', $driver) }}">@include('drivers._form', ['isEdit' => true])</form>
    </x-ui.form-card>
</x-layouts.app>
