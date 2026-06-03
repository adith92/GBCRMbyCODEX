@props(['status' => 'unknown'])

@php
    $normalized = str_replace('_', '-', strtolower((string) $status));

    $palette = match ($normalized) {
        'active', 'available', 'approved', 'paid', 'completed', 'present' => 'border border-emerald-200 bg-emerald-50 text-emerald-700',
        'pending', 'scheduled', 'draft', 'partial', 'expiring', 'po' => 'border border-amber-200 bg-amber-50 text-amber-700',
        'maintenance', 'assigned', 'confirmed', 'sent', 'in-progress' => 'border border-sky-200 bg-sky-50 text-[#185FA5]',
        'hold', 'cancelled', 'expired', 'overdue', 'absent', 'sick', 'leave', 'used' => 'border border-rose-200 bg-rose-50 text-rose-700',
        default => 'border border-slate-200 bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.15em]', $palette]) }}>
    {{ str_replace('-', ' ', $normalized) }}
</span>
