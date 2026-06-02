<x-layouts.app :title="'Global Search'" :header="'Search'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Search', 'url' => route('search.index')],
    ]" />

    <x-ui.page-header title="Global search" eyebrow="Discovery" description="Jump quickly across CRM, fleet, drivers, bookings, finance, and maintenance from one search surface." />

    <x-ui.form-card title="Search workspace" description="Search across entities you are allowed to access.">
        <form method="GET" class="flex flex-col gap-3 md:flex-row">
            <input type="text" name="q" value="{{ $query }}" placeholder="Try booking number, client name, plate number, driver, or invoice" class="ui-input md:flex-1">
            <x-ui.action-button type="submit" variant="primary">Search</x-ui.action-button>
        </form>
    </x-ui.form-card>

    <x-ui.table-card title="Results" description="Permission-aware search results across the current workspace.">
        @if ($query === '')
            <div class="p-5">
                <x-ui.empty-state title="Start with a keyword" description="Search becomes especially useful during demos when you need to jump straight to a record." />
            </div>
        @elseif ($results->isEmpty())
            <div class="p-5">
                <x-ui.empty-state title="No results found" description="Try a broader term or use an exact booking, plate, client, or invoice keyword." />
            </div>
        @else
            <div class="space-y-3 p-5">
                @foreach ($results as $result)
                    <a href="{{ $result['url'] }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $result['type'] }}</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $result['label'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $result['meta'] }}</p>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">Open</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
