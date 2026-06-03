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
    <a href="{{ $href }}" {{ $attributes->class(['ui-card ui-kpi-hover group block overflow-hidden']) }}>
        <div class="h-1 {{ $scheme['line'] }}"></div>
        <div class="p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</p>
                    <p class="mt-3 text-[30px] font-semibold tracking-[-0.04em] text-[#042C53]">{{ $value }}</p>
                </div>
                <span class="rounded-full border px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] transition {{ $scheme['pill'] }}">{{ $linkLabel }}</span>
            </div>
            @if ($hint)
                <p class="mt-2 text-sm leading-5 text-slate-500">{{ $hint }}</p>
            @endif
        </div>
    </a>
@else
    <div {{ $attributes->class(['ui-card overflow-hidden']) }}>
        <div class="h-1 {{ $scheme['line'] }}"></div>
        <div class="p-4">
            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</p>
            <p class="mt-3 text-[30px] font-semibold tracking-[-0.04em] text-[#042C53]">{{ $value }}</p>
            @if ($hint)
                <p class="mt-2 text-sm leading-5 text-slate-500">{{ $hint }}</p>
            @endif
        </div>
    </div>
@endif
