<x-layouts.app :title="'BlueERP Dashboard'" :header="'GM Dashboard (Initial)'">
    <section class="grid gap-4 md:grid-cols-3">
        <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Total Clients</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">0</p>
        </article>
        <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Active Bookings</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">0</p>
        </article>
        <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm text-slate-500">Open Invoices</p>
            <p class="mt-2 text-2xl font-semibold text-slate-900">0</p>
        </article>
    </section>

    <section class="mt-6 rounded-lg border border-dashed border-slate-300 bg-white p-4">
        <h3 class="text-base font-semibold text-slate-900">Checkpoint 0.1</h3>
        <p class="mt-2 text-sm text-slate-600">
            Foundation dashboard shell ready. Next checkpoint will connect RBAC middleware and role-specific navigation visibility.
        </p>
    </section>
</x-layouts.app>
