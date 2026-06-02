<x-layouts.app :title="'Create Client'" :header="'Create Client'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'CRM', 'url' => route('crm.index')],
        ['label' => 'Clients', 'url' => route('crm.clients.index')],
        ['label' => 'Create Client'],
    ]" />

    <x-ui.page-header title="Create client" eyebrow="CRM" description="Add a commercial account with the minimum data needed for booking and finance flows." />

    <x-ui.form-card title="Client form" description="Commercial identity and billing profile used across the CRM and finance modules.">
        <form method="POST" action="{{ route('crm.clients.store') }}">@include('clients._form', ['isEdit' => false])</form>
    </x-ui.form-card>
</x-layouts.app>
