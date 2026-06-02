<x-layouts.app :title="'Edit Client'" :header="'Edit Client'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'CRM', 'url' => route('crm.index')],
        ['label' => 'Clients', 'url' => route('crm.clients.index')],
        ['label' => $client->name, 'url' => route('crm.clients.show', $client)],
        ['label' => 'Edit Client'],
    ]" />

    <x-ui.page-header :title="'Edit '.$client->name" eyebrow="CRM" description="Refine account metadata without leaving the client management flow." />

    <x-ui.form-card title="Client form" description="Update commercial identity, billing, and status metadata for this client.">
        <form method="POST" action="{{ route('crm.clients.update', $client) }}">@include('clients._form', ['isEdit' => true])</form>
    </x-ui.form-card>
</x-layouts.app>
