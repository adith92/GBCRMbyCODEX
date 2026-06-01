<x-layouts.app :title="'Create Client'" :header="'Create Client'">
    <section class="rounded-lg border border-slate-200 bg-white p-4"><form method="POST" action="{{ route('crm.clients.store') }}">@include('clients._form', ['isEdit' => false])</form></section>
</x-layouts.app>
