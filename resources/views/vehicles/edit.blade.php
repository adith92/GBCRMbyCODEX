<x-layouts.app :title="'Edit Vehicle'" :header="'Edit Vehicle'">
    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <form method="POST" action="{{ route('fleet.vehicles.update', $vehicle) }}">
            @include('vehicles._form', ['isEdit' => true])
        </form>
    </section>
</x-layouts.app>
