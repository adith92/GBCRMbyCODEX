@props(['status' => 'unknown'])

@php
    $normalized = str_replace('_', '-', strtolower((string) $status));

    $palette = match ($normalized) {
        'active', 'available', 'approved', 'paid', 'completed', 'present' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'pending', 'scheduled', 'draft', 'partial', 'expiring', 'po' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'maintenance', 'assigned', 'confirmed', 'sent', 'in-progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'hold', 'cancelled', 'expired', 'overdue', 'absent', 'sick', 'leave', 'used' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        default => 'bg-slate-100 text-slate-700 ring-slate-500/20',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] ring-1 ring-inset', $palette]) }}>
    {{ str_replace('-', ' ', $normalized) }}
</span>
