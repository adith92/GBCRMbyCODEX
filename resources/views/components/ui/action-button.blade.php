@props(['href' => null, 'variant' => 'secondary', 'type' => 'button'])

@php
    $variants = [
        'primary' => 'border border-[#042C53] bg-[#042C53] text-white hover:bg-[#053561] hover:border-[#053561] shadow-sm',
        'secondary' => 'border border-[#D7DCE3] bg-white text-slate-700 hover:bg-slate-50',
        'success' => 'border border-emerald-600 bg-emerald-600 text-white hover:bg-emerald-700',
        'danger' => 'border border-rose-600 bg-rose-600 text-white hover:bg-rose-700',
        'ghost' => 'border border-transparent bg-[#EEF4FA] text-[#185FA5] hover:bg-[#E2ECF8]',
    ];
    $classes = 'inline-flex items-center justify-center rounded-[9px] px-4 py-2.5 text-sm font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-[#378ADD]/20';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class([$classes, $variants[$variant] ?? $variants['secondary']]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes, $variants[$variant] ?? $variants['secondary']]) }}>
        {{ $slot }}
    </button>
@endif
