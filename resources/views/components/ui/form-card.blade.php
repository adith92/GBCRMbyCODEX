@props(['title' => null, 'description' => null])

<section {{ $attributes->class(['ui-card p-5 sm:p-6']) }}>
    @if ($title || $description)
        <div class="mb-5">
            @if ($title)
                <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
            @endif
            @if ($description)
                <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
            @endif
        </div>
    @endif
    {{ $slot }}
</section>
