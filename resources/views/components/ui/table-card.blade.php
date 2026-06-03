@props(['title' => null, 'description' => null])

<section {{ $attributes->class(['ui-card overflow-hidden']) }}>
    @if ($title || $description || isset($actions))
        <div class="border-b border-[#E5E7EB] px-4 py-3">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    @if ($title)
                        <h3 class="text-[14px] font-semibold text-[#042C53]">{{ $title }}</h3>
                    @endif
                    @if ($description)
                        <p class="mt-1 text-[13px] text-slate-500">{{ $description }}</p>
                    @endif
                </div>
                @if (isset($actions))
                    <div class="flex flex-wrap gap-3 no-print">{{ $actions }}</div>
                @endif
            </div>
        </div>
    @endif
    <div class="p-0">
        {{ $slot }}
    </div>
</section>
