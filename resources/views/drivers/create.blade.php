<x-layouts.app :title="'Create Driver'" :header="'Create Driver'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Drivers', 'url' => route('drivers.index')],
        ['label' => 'Create Driver'],
    ]" />

    <x-ui.page-header title="Create driver" eyebrow="Drivers" description="Add a dispatch-ready driver profile with pool assignment and license metadata." />

    <x-ui.form-card title="Driver form" description="Identity, assignment pool, and compliance fields used across pool and HR-adjacent views.">
        <form method="POST" action="{{ route('drivers.store') }}">@include('drivers._form', ['isEdit' => false])</form>
    </x-ui.form-card>
</x-layouts.app>
