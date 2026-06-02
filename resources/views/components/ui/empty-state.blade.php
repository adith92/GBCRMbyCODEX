@props(['title' => 'No data yet', 'description' => 'There is nothing to show right now.'])

<div {{ $attributes->class(['rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 px-6 py-10 text-center']) }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-100">
        <span class="text-lg">i</span>
    </div>
    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-500">{{ $description }}</p>
    @if (isset($actions))
        <div class="mt-5 flex flex-wrap justify-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
