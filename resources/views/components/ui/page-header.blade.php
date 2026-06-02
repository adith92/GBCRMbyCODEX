@props(['title', 'eyebrow' => null, 'description' => null])

<div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div class="space-y-2">
        @if ($eyebrow)
            <p class="ui-section-title">{{ $eyebrow }}</p>
        @endif
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950 sm:text-3xl">{{ $title }}</h1>
            @if ($description)
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $description }}</p>
            @endif
        </div>
    </div>

    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
