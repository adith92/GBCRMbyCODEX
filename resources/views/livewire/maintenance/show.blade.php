<x-layouts.app :title="'Maintenance Detail'" :header="'Maintenance / Detail'">
    <x-breadcrumbs :items="$breadcrumbs" />

    <x-ui.page-header :title="$maintenanceLog->title" eyebrow="Maintenance Detail" description="Vehicle readiness context, service timeline, and operational notes for this maintenance case.">
        <x-slot:actions>
            <x-ui.status-badge :status="$maintenanceLog->status" />
            <x-back-link :fallback="route('maintenance.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Maintenance snapshot" description="Review vehicle impact, service timing, and owner context from one detail page.">
        <dl class="ui-meta-grid">
            <div class="ui-meta-item"><dt>Title</dt><dd>{{ $maintenanceLog->title }}</dd></div>
            <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$maintenanceLog->status" /></dd></div>
            <div class="ui-meta-item"><dt>Vehicle</dt><dd>@if($maintenanceLog->vehicle)<a href="{{ route('fleet.vehicles.show', $maintenanceLog->vehicle) }}" class="ui-link">{{ $maintenanceLog->vehicle->plate_number }}</a>@else - @endif</dd></div>
            <div class="ui-meta-item"><dt>Reported By</dt><dd>{{ $maintenanceLog->reportedBy?->name ?? '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Start At</dt><dd>{{ $maintenanceLog->start_at?->format('Y-m-d H:i') ?? '-' }}</dd></div>
            <div class="ui-meta-item"><dt>End At</dt><dd>{{ $maintenanceLog->end_at?->format('Y-m-d H:i') ?? '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Cost</dt><dd>{{ number_format($maintenanceLog->cost, 2) }}</dd></div>
            <div class="ui-meta-item md:col-span-2 xl:col-span-3"><dt>Notes</dt><dd>{{ $maintenanceLog->notes ?: '-' }}</dd></div>
        </dl>
        <div class="mt-5 flex flex-wrap gap-3">
            @can('maintenance.update')
                <x-ui.action-button :href="route('maintenance.edit', $maintenanceLog)" variant="secondary">Edit</x-ui.action-button>
            @endcan
        </div>
    </x-ui.form-card>
</x-layouts.app>
