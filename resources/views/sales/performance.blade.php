@php
    $idr = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
@endphp

<x-layouts.app :title="'Sales Performance'" :header="'Sales / Performance'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sales', 'url' => route('sales.index')],
        ['label' => $user->name, 'url' => route('sales.performance', $user)],
    ]" />

    <x-ui.page-header title="{{ $user->name }}" eyebrow="Sales Performance" description="Drill-down cepat untuk client touchpoint, booking pipeline, dan kontribusi revenue sales user ini." />

    <section class="ui-compact-grid md:grid-cols-2 xl:grid-cols-5">
        <x-ui.stat-card label="Clients" :value="$summary['total_clients']" hint="Client touched by this sales user." tone="blue" />
        <x-ui.stat-card label="Bookings" :value="$summary['total_bookings']" hint="Total booking requests created by this sales user." tone="emerald" />
        <x-ui.stat-card label="Confirmed" :value="$summary['confirmed_bookings']" hint="Confirmed and completed bookings." tone="amber" />
        <x-ui.stat-card label="Revenue" :value="$idr($summary['total_revenue'])" hint="Invoice value linked to this sales pipeline." tone="slate" />
        <x-ui.stat-card label="Paid" :value="$idr($summary['total_paid'])" hint="Collected payment linked to this sales pipeline." tone="blue" />
    </section>

    <section class="grid gap-4 xl:grid-cols-2">
        <x-ui.table-card title="Latest bookings" description="Booking terbaru yang diminta sales user ini.">
            @if ($bookings->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No bookings yet" description="Booking yang dibuat user ini akan muncul di sini." />
                </div>
            @else
                <div class="ui-table-wrap">
                    <table class="ui-table">
                        <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Client</th>
                            <th>Pool</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td><a href="{{ route('bookings.show', $booking) }}" class="ui-link font-semibold text-slate-900">{{ $booking->booking_number }}</a></td>
                                <td>@if($booking->client)<a href="{{ route('crm.clients.show', $booking->client) }}" class="ui-link">{{ $booking->client->name }}</a>@else - @endif</td>
                                <td>{{ $booking->pool?->name ?? '-' }}</td>
                                <td><x-ui.status-badge :status="$booking->status" /></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.table-card>

        <x-ui.table-card title="Latest invoices" description="Invoice terbaru yang berasal dari booking user ini.">
            @if ($invoices->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No invoices yet" description="Invoice dari pipeline sales ini akan muncul setelah PO dan billing terbentuk." />
                </div>
            @else
                <div class="ui-table-wrap">
                    <table class="ui-table">
                        <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td><a href="{{ route('finance.invoices.show', $invoice) }}" class="ui-link font-semibold text-slate-900">{{ $invoice->invoice_number }}</a></td>
                                <td>@if($invoice->client)<a href="{{ route('crm.clients.show', $invoice->client) }}" class="ui-link">{{ $invoice->client->name }}</a>@else - @endif</td>
                                <td><x-ui.status-badge :status="$invoice->status" /></td>
                                <td>{{ $idr($invoice->total) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-ui.table-card>
    </section>
</x-layouts.app>
