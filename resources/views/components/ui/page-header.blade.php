@props(['title', 'eyebrow' => null, 'description' => null])

<div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
    <div class="space-y-2">
        @if ($eyebrow)
            <p class="ui-section-title">{{ $eyebrow }}</p>
        @endif
        <div>
            <h1 class="text-[28px] font-semibold tracking-[-0.03em] text-[#042C53] sm:text-[32px]">{{ $title }}</h1>
            @if ($description)
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-500">{{ $description }}</p>
            @endif
        </div>
    </div>

    @if (isset($actions))
        <div class="flex flex-wrap items-center gap-3 no-print">
            {{ $actions }}
        </div>
    @endif
</div>
