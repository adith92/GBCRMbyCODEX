<x-layouts.app :title="'BlueERP - '.$header" :header="$header">
    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-900">{{ $header }}</h3>
        <p class="mt-2 text-sm text-slate-600">{{ $message }}</p>
    </section>
</x-layouts.app>
