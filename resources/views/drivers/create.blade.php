<x-layouts.app :title="'Create Driver'" :header="'Create Driver'">
    <section class="rounded-lg border border-slate-200 bg-white p-4"><form method="POST" action="{{ route('drivers.store') }}">@include('drivers._form', ['isEdit' => false])</form></section>
</x-layouts.app>
