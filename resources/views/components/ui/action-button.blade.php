@props(['href' => null, 'variant' => 'secondary', 'type' => 'button'])

@php
    $variants = [
        'primary' => 'bg-blue-700 text-white hover:bg-blue-800 border border-blue-700 shadow-sm',
        'secondary' => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50',
        'success' => 'border border-emerald-700 bg-emerald-600 text-white hover:bg-emerald-700',
        'danger' => 'border border-rose-700 bg-rose-600 text-white hover:bg-rose-700',
        'ghost' => 'border border-transparent bg-slate-100 text-slate-700 hover:bg-slate-200',
    ];
    $classes = 'inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold transition';
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
