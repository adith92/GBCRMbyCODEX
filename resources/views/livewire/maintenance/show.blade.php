@php($idr = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.'))
<x-layouts.app :title="'Maintenance Detail'" :header="'Maintenance / Detail'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <x-ui.page-header :title="$maintenanceLog->title" eyebrow="Maintenance Detail" description="Vehicle readiness context, service timeline, dan operational notes untuk case maintenance ini.">
        <x-slot:actions>
            <x-ui.status-badge :status="$maintenanceLog->status" />
            <x-back-link :fallback="route('maintenance.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    <section class="grid gap-3 md:grid-cols-4">
        <x-ui.stat-card label="Vehicle" :value="$maintenanceLog->vehicle?->plate_number ?? '-'" hint="🚐 affected unit" tone="blue" />
        <x-ui.stat-card label="Status" :value="ucwords(str_replace('_', ' ', $maintenanceLog->status))" hint="🛠️ service stage" tone="amber" />
        <x-ui.stat-card label="Cost" :value="$idr($maintenanceLog->cost)" hint="💸 estimated or actual" tone="rose" />
        <x-ui.stat-card label="Reported By" :value="$maintenanceLog->reportedBy?->name ?? '-'" hint="👤 owner case" tone="slate" />
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
        <x-ui.form-card title="Maintenance snapshot" description="Review vehicle impact, service timing, dan owner context dari satu detail page.">
            <dl class="ui-meta-grid">
                <div class="ui-meta-item"><dt>Title</dt><dd>{{ $maintenanceLog->title }}</dd></div>
                <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$maintenanceLog->status" /></dd></div>
                <div class="ui-meta-item"><dt>Vehicle</dt><dd>@if($maintenanceLog->vehicle)<a href="{{ route('fleet.vehicles.show', $maintenanceLog->vehicle) }}" class="ui-link">{{ $maintenanceLog->vehicle->plate_number }}</a>@else - @endif</dd></div>
                <div class="ui-meta-item"><dt>Reported By</dt><dd>{{ $maintenanceLog->reportedBy?->name ?? '-' }}</dd></div>
                <div class="ui-meta-item"><dt>Start At</dt><dd>{{ $maintenanceLog->start_at?->format('Y-m-d H:i') ?? '-' }}</dd></div>
                <div class="ui-meta-item"><dt>End At</dt><dd>{{ $maintenanceLog->end_at?->format('Y-m-d H:i') ?? '-' }}</dd></div>
                <div class="ui-meta-item"><dt>Cost</dt><dd>{{ $idr($maintenanceLog->cost) }}</dd></div>
                <div class="ui-meta-item md:col-span-2 xl:col-span-3"><dt>Notes</dt><dd>{{ $maintenanceLog->notes ?: '-' }}</dd></div>
            </dl>
            <div class="mt-5 flex flex-wrap gap-3">
                @can('maintenance.update')
                    <x-ui.action-button :href="route('maintenance.edit', $maintenanceLog)" variant="secondary">Edit</x-ui.action-button>
                @endcan
            </div>
        </x-ui.form-card>

        <x-ui.table-card title="Service timeline" description="Visual progres maintenance agar operation lebih cepat membaca kondisinya.">
            <div class="ui-timeline space-y-4 p-4">
                <div class="ui-timeline-item rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Stage 1</p>
                    <p class="mt-1 font-semibold text-[#042C53]">Case dilaporkan</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $maintenanceLog->created_at?->format('Y-m-d H:i') }} oleh {{ $maintenanceLog->reportedBy?->name ?? '-' }}.</p>
                </div>
                <div class="ui-timeline-item rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Stage 2</p>
                    <p class="mt-1 font-semibold text-[#042C53]">Mulai pengerjaan</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $maintenanceLog->start_at ? $maintenanceLog->start_at->format('Y-m-d H:i') : 'Belum dijadwalkan mulai.' }}</p>
                </div>
                <div class="ui-timeline-item rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Stage 3</p>
                    <p class="mt-1 font-semibold text-[#042C53]">Selesai / release unit</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $maintenanceLog->end_at ? $maintenanceLog->end_at->format('Y-m-d H:i') : 'Belum ada waktu selesai.' }}</p>
                </div>
            </div>
        </x-ui.table-card>
    </section>
</x-layouts.app>
