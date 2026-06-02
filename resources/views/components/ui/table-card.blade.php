@props(['title' => null, 'description' => null])

<section {{ $attributes->class(['ui-card overflow-hidden']) }}>
    @if ($title || $description || isset($actions))
        <div class="border-b border-slate-200/80 px-5 py-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    @if ($title)
                        <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                    @endif
                    @if ($description)
                        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
                    @endif
                </div>
                @if (isset($actions))
                    <div class="flex flex-wrap gap-3">{{ $actions }}</div>
                @endif
            </div>
        </div>
    @endif
    <div class="p-0">
        {{ $slot }}
    </div>
</section>
