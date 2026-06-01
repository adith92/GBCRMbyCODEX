<x-layouts.app :title="'Create Vehicle'" :header="'Create Vehicle'">
    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <form method="POST" action="{{ route('fleet.vehicles.store') }}">
            @include('vehicles._form', ['isEdit' => false])
        </form>
    </section>
</x-layouts.app>
