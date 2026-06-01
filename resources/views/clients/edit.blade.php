<x-layouts.app :title="'Edit Client'" :header="'Edit Client'">
    <section class="rounded-lg border border-slate-200 bg-white p-4"><form method="POST" action="{{ route('crm.clients.update', $client) }}">@include('clients._form', ['isEdit' => true])</form></section>
</x-layouts.app>
