<x-layouts.app :title="'Edit Driver'" :header="'Edit Driver'">
    <section class="rounded-lg border border-slate-200 bg-white p-4"><form method="POST" action="{{ route('drivers.update', $driver) }}">@include('drivers._form', ['isEdit' => true])</form></section>
</x-layouts.app>
