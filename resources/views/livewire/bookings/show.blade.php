<x-layouts.app :title="'Booking Detail'" :header="'Bookings / Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Bookings', 'url' => route('bookings.index')],
        ['label' => $booking->booking_number, 'url' => route('bookings.show', $booking)],
    ]" />

    <x-ui.page-header :title="$booking->booking_number" eyebrow="Booking Detail" description="Review commercial context, assignment readiness, linked finance documents, and dispatch history.">
        <x-slot:actions>
            <x-ui.status-badge :status="$booking->status" />
            <x-back-link :fallback="route('bookings.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    @if ($errorMessage)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ $errorMessage }}</div>
    @endif

    <x-ui.form-card title="Booking snapshot" description="Core operational and commercial data for this request.">
        <dl class="ui-meta-grid">
            <div class="ui-meta-item"><dt>Booking Number</dt><dd>{{ $booking->booking_number }}</dd></div>
            <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$booking->status" /></dd></div>
            <div class="ui-meta-item"><dt>Client</dt><dd>@if($booking->client)<a href="{{ route('crm.clients.show', $booking->client) }}" class="ui-link">{{ $booking->client->name }}</a>@else - @endif</dd></div>
            <div class="ui-meta-item"><dt>Pool</dt><dd>{{ $booking->pool?->name ?? '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Vehicle</dt><dd>@if($booking->vehicle)<a href="{{ route('fleet.vehicles.show', $booking->vehicle) }}" class="ui-link">{{ $booking->vehicle->plate_number }}</a>@else - @endif</dd></div>
            <div class="ui-meta-item"><dt>Driver</dt><dd>@if($booking->driver)<a href="{{ route('drivers.show', $booking->driver) }}" class="ui-link">{{ $booking->driver->name }}</a>@else - @endif</dd></div>
            <div class="ui-meta-item"><dt>Start</dt><dd>{{ $booking->start_datetime?->format('Y-m-d H:i') }}</dd></div>
            <div class="ui-meta-item"><dt>End</dt><dd>{{ $booking->end_datetime?->format('Y-m-d H:i') }}</dd></div>
            <div class="ui-meta-item md:col-span-2 xl:col-span-3"><dt>Pickup</dt><dd>{{ $booking->pickup_location ?: '-' }}</dd></div>
            <div class="ui-meta-item md:col-span-2 xl:col-span-3"><dt>Destination</dt><dd>{{ $booking->destination ?: '-' }}</dd></div>
            <div class="ui-meta-item md:col-span-2 xl:col-span-3"><dt>Notes</dt><dd>{{ $booking->notes ?: '-' }}</dd></div>
        </dl>

        <div class="mt-5 flex flex-wrap gap-3">
            @can('bookings.update')
                <x-ui.action-button :href="route('bookings.edit', $booking)" variant="secondary">Edit</x-ui.action-button>
            @endcan
            @can('pool.assign-driver')
                @if (in_array($booking->status, ['pending', 'assigned'], true))
                    <x-ui.action-button :href="route('pool.assign', $booking)" variant="primary">Assign Driver & Vehicle</x-ui.action-button>
                @endif
            @endcan
            @can('bookings.approve')
                @if ($booking->status === 'assigned')
                    <x-ui.action-button wire:click="confirm" wire:confirm="Confirm this booking?" variant="success">Confirm Booking</x-ui.action-button>
                @endif
            @endcan
            @can('bookings.cancel')
                @if (in_array($booking->status, ['pending', 'assigned', 'confirmed'], true))
                    <x-ui.action-button wire:click="cancel" wire:confirm="Cancel this booking?" variant="danger">Cancel Booking</x-ui.action-button>
                @endif
            @endcan
        </div>
    </x-ui.form-card>

    @if($booking->purchaseOrders->isNotEmpty())
        <x-ui.table-card title="Purchase Orders" description="Finance documents already linked to this booking.">
            <div class="space-y-3 p-5 text-sm">
                @foreach($booking->purchaseOrders as $purchaseOrder)
                    <a href="{{ route('finance.purchase-orders.show', $purchaseOrder) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $purchaseOrder->po_number }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ number_format($purchaseOrder->total, 2) }}</p>
                            </div>
                            <x-ui.status-badge :status="$purchaseOrder->status" />
                        </div>
                    </a>
                @endforeach
            </div>
        </x-ui.table-card>
    @endif

    @if($booking->purchaseOrders->flatMap->invoices->isNotEmpty())
        <x-ui.table-card title="Invoices" description="Invoices generated through related purchase orders.">
            <div class="space-y-3 p-5 text-sm">
                @foreach($booking->purchaseOrders->flatMap->invoices as $invoice)
                    <a href="{{ route('finance.invoices.show', $invoice) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-emerald-200 hover:bg-emerald-50/40">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $invoice->invoice_number }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ number_format($invoice->total, 2) }}</p>
                            </div>
                            <x-ui.status-badge :status="$invoice->status" />
                        </div>
                    </a>
                @endforeach
            </div>
        </x-ui.table-card>
    @endif

    <x-ui.table-card title="Driver Assignment History" description="Track reassignment and release events for dispatch visibility.">
        @if($booking->driverAssignments->isEmpty())
            <div class="p-5">
                <x-ui.empty-state title="No assignment history yet" description="Assignments will be listed here once the pool team starts dispatching." />
            </div>
        @else
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>Assigned At</th>
                        <th>Driver</th>
                        <th>Vehicle</th>
                        <th>By</th>
                        <th>Released</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($booking->driverAssignments as $assignment)
                        <tr>
                            <td>{{ $assignment->assigned_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $assignment->driver?->name ?? '-' }}</td>
                            <td>{{ $assignment->vehicle?->plate_number ?? '-' }}</td>
                            <td>{{ $assignment->assignedBy?->name ?? '-' }}</td>
                            <td>{{ $assignment->released_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
