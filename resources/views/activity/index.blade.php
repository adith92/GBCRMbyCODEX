<x-layouts.app :title="'Recent Activity'" :header="'Activity'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Activity', 'url' => route('activity.index')],
    ]" />

    <x-ui.page-header title="Recent activity" eyebrow="Workspace Activity" description="A lightweight operational timeline spanning bookings, invoices, payments, maintenance, and CRM follow-up." />

    @if ($summary->isNotEmpty())
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ($summary as $item)
                <a href="{{ route('activity.index', ['type' => $item['key']]) }}" class="ui-card-muted px-4 py-4 transition hover:border-slate-300 hover:bg-white">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] {{ $type === $item['key'] ? 'text-blue-700' : 'text-slate-500' }}">{{ $item['label'] }}</p>
                    <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">{{ $item['count'] }}</p>
                </a>
            @endforeach
        </section>
    @endif

    <div class="flex flex-wrap gap-2">
        @foreach ($typeOptions as $key => $label)
            <a href="{{ route('activity.index', ['type' => $key]) }}" class="rounded-full border px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] transition {{ $type === $key ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <x-ui.table-card title="Timeline" description="Latest movement from the modules available to your role.">
        @if ($items->isEmpty())
            <div class="p-5">
                <x-ui.empty-state title="No activity visible" description="Activity items will appear once your accessible modules start moving." />
            </div>
        @else
            <div class="space-y-3 p-5">
                @foreach ($items as $item)
                    <a href="{{ $item['url'] }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $item['type'] }}</p>
                                <p class="mt-1 font-semibold text-slate-900">{{ $item['title'] }}</p>
                                <p class="mt-1 text-sm text-slate-500">{{ $item['description'] }}</p>
                            </div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $item['timestamp']?->format('Y-m-d H:i') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
