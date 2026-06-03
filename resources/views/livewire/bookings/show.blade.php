<x-layouts.app :title="'Booking Detail'" :header="'Bookings / Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Bookings', 'url' => route('bookings.index')],
        ['label' => $booking->booking_number, 'url' => route('bookings.show', $booking)],
    ]" />

    <x-ui.page-header :title="$booking->booking_number" eyebrow="Booking Detail" description="Review commercial context, assignment readiness, linked finance documents, dan audit dispatch history dari satu layar.">
        <x-slot:actions>
            <x-ui.status-badge :status="$booking->status" />
            <x-back-link :fallback="route('bookings.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    @if ($errorMessage)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ $errorMessage }}</div>
    @endif

    <section class="grid gap-4 md:grid-cols-3">
        <x-ui.stat-card label="Client" :value="$booking->client?->name ?? '-'" hint="👥 owner request" tone="blue" />
        <x-ui.stat-card label="Assignment History" :value="$booking->driverAssignments->count()" hint="🧭 audit trail" tone="emerald" />
        <x-ui.stat-card label="Finance Docs" :value="$booking->purchaseOrders->count() + $booking->purchaseOrders->flatMap->invoices->count()" hint="🧾 linked docs" tone="amber" />
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.05fr_0.95fr]">
        <x-ui.form-card title="Booking snapshot" description="Core operational dan commercial data untuk request ini.">
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

        <x-ui.table-card title="Dispatch audit trail" description="Visual ringkas perubahan assignment driver dan vehicle untuk booking ini.">
            @if($booking->driverAssignments->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No assignment history yet" description="Trail akan muncul setelah pool mulai assign atau reassign driver dan kendaraan." />
                </div>
            @else
                <div class="ui-timeline space-y-4 p-4">
                    @foreach($booking->driverAssignments as $assignment)
                        <div class="ui-timeline-item rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#042C53]">{{ $assignment->driver?->name ?? 'No driver' }} → {{ $assignment->vehicle?->plate_number ?? 'No vehicle' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Assigned by {{ $assignment->assignedBy?->name ?? '-' }} · {{ $assignment->assigned_at?->format('Y-m-d H:i') }}</p>
                                </div>
                                <span class="rounded-full bg-[#F4F8FD] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">{{ $assignment->released_at ? 'Reassigned' : 'Active' }}</span>
                            </div>
                            @if($assignment->released_at)
                                <p class="mt-2 text-xs text-slate-500">Released at {{ $assignment->released_at->format('Y-m-d H:i') }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>
    </section>

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
</x-layouts.app>
