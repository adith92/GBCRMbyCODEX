@props(['label', 'value', 'hint' => null, 'tone' => 'blue', 'href' => null, 'linkLabel' => 'Open'])

@php
    $tones = [
        'blue' => ['line' => 'bg-[#185FA5]', 'pill' => 'bg-[#EAF2FB] text-[#185FA5] border-[#D3E3F6]'],
        'emerald' => ['line' => 'bg-emerald-500', 'pill' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'amber' => ['line' => 'bg-amber-500', 'pill' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'rose' => ['line' => 'bg-rose-500', 'pill' => 'bg-rose-50 text-rose-700 border-rose-200'],
        'slate' => ['line' => 'bg-[#042C53]', 'pill' => 'bg-slate-100 text-slate-700 border-slate-200'],
    ];
    $scheme = $tones[$tone] ?? $tones['blue'];
@endphp

@if ($href)
    <a href="{{ $href }}" title="{{ $hint }}" {{ $attributes->class(['ui-dashboard-card ui-kpi-hover group block overflow-hidden']) }}>
        <div class="h-1 {{ $scheme['line'] }}"></div>
        <div class="ui-compact-card">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-[24px] font-semibold tracking-[-0.04em] text-[#042C53] xl:text-[26px]">{{ $value }}</p>
                    @if ($hint)
                        <p class="ui-kpi-caption line-clamp-1">{{ $hint }}</p>
                    @endif
                </div>
                <span class="rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] transition {{ $scheme['pill'] }}">{{ $linkLabel }}</span>
            </div>
        </div>
    </a>
@else
    <div title="{{ $hint }}" {{ $attributes->class(['ui-dashboard-card overflow-hidden']) }}>
        <div class="h-1 {{ $scheme['line'] }}"></div>
        <div class="ui-compact-card">
            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-[24px] font-semibold tracking-[-0.04em] text-[#042C53] xl:text-[26px]">{{ $value }}</p>
            @if ($hint)
                <p class="ui-kpi-caption line-clamp-1">{{ $hint }}</p>
            @endif
        </div>
    </div>
@endif
