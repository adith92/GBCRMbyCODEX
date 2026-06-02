@props(['label', 'value', 'hint' => null, 'tone' => 'blue', 'href' => null, 'linkLabel' => 'Open'])

@php
    $tones = [
        'blue' => 'from-blue-600 via-blue-700 to-sky-600',
        'emerald' => 'from-emerald-600 via-emerald-700 to-teal-600',
        'amber' => 'from-amber-500 via-amber-600 to-orange-500',
        'rose' => 'from-rose-500 via-rose-600 to-red-500',
        'slate' => 'from-slate-700 via-slate-800 to-slate-600',
    ];
    $gradient = $tones[$tone] ?? $tones['blue'];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class(['ui-card group block overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg']) }}>
        <div class="h-1.5 bg-gradient-to-r {{ $gradient }}"></div>
        <div class="p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $label }}</p>
                    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
                </div>
                <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500 transition group-hover:border-blue-200 group-hover:text-blue-700">{{ $linkLabel }}</span>
            </div>
            @if ($hint)
                <p class="mt-2 text-sm text-slate-500">{{ $hint }}</p>
            @endif
        </div>
    </a>
@else
    <div {{ $attributes->class(['ui-card overflow-hidden']) }}>
        <div class="h-1.5 bg-gradient-to-r {{ $gradient }}"></div>
        <div class="p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $label }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
            @if ($hint)
                <p class="mt-2 text-sm text-slate-500">{{ $hint }}</p>
            @endif
        </div>
    </div>
@endif
