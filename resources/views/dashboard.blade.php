@php
    $user = auth()->user();
    $activeClients = \App\Models\Client::query()->where('status', 'active')->count();
    $activeBookings = \App\Models\Booking::query()->whereIn('status', ['pending', 'assigned', 'confirmed'])->count();
    $pendingBookings = \App\Models\Booking::query()->where('status', 'pending')->count();
    $availableVehicles = \App\Models\Vehicle::query()->where('status', 'available')->count();
    $vehiclesInPo = \App\Models\Vehicle::query()->where('status', 'po')->count();
    $vehiclesInMaintenance = \App\Models\Vehicle::query()->where('status', 'maintenance')->count();
    $outstandingInvoiceAmount = (float) \App\Models\Invoice::query()->whereIn('status', ['sent', 'partial', 'overdue'])->sum('total')
        - (float) \App\Models\Invoice::query()->whereIn('status', ['sent', 'partial', 'overdue'])->sum('paid_amount');
    $overdueInvoices = \App\Models\Invoice::query()->where('status', 'overdue')->count();
    $todayPoolQueueCount = \App\Models\Booking::query()->whereDate('start_datetime', today())->whereIn('status', ['pending', 'assigned'])->count();
    $latestBookings = \App\Models\Booking::query()->with('client')->latest()->limit(5)->get();
    $latestPayments = \App\Models\Payment::query()->with('invoice')->latest()->limit(5)->get();
    $latestMaintenanceLogs = \App\Models\MaintenanceLog::query()->with('vehicle')->latest()->limit(5)->get();
@endphp

<x-layouts.app :title="'Dashboard'" :header="'Dashboard'">
    <x-breadcrumbs :items="[['label' => 'Dashboard', 'url' => route('dashboard')]]" />

    <x-ui.page-header title="Enterprise operations at a glance" eyebrow="GM Dashboard" description="Track client activity, dispatch readiness, finance exposure, and maintenance signals from a single Bluebird-inspired command center.">
        <x-slot:actions>
            <div class="ui-card-muted px-4 py-3 text-right">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Current Role</p>
                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $user?->getRoleNames()->join(', ') }}</p>
            </div>
        </x-slot:actions>
    </x-ui.page-header>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <x-ui.stat-card label="Total Active Clients" :value="$activeClients" hint="Accounts currently ready for commercial follow-up." tone="blue" :href="route('crm.clients.index', ['status' => 'active'])" link-label="Open CRM" />
        <x-ui.stat-card label="Active Bookings" :value="$activeBookings" hint="Pending, assigned, and confirmed workload in motion." tone="emerald" :href="route('bookings.index')" link-label="Open Pipeline" />
        <x-ui.stat-card label="Pending Bookings" :value="$pendingBookings" hint="Need dispatch review or commercial follow-up." tone="amber" :href="route('bookings.index', ['status' => 'pending'])" link-label="Review Queue" />
        <x-ui.stat-card label="Available Vehicles" :value="$availableVehicles" hint="Fleet units currently ready for assignment." tone="blue" :href="route('fleet.vehicles.index', ['status' => 'available'])" link-label="Open Fleet" />
        <x-ui.stat-card label="Vehicles In PO" :value="$vehiclesInPo" hint="Already committed to upcoming dispatch activity." tone="amber" :href="route('fleet.vehicles.index', ['status' => 'po'])" link-label="Open Fleet" />
        <x-ui.stat-card label="Vehicles In Maintenance" :value="$vehiclesInMaintenance" hint="Temporarily unavailable due to workshop handling." tone="rose" :href="route('maintenance.index', ['status' => 'in_progress'])" link-label="Open Maintenance" />
        <x-ui.stat-card label="Outstanding Invoices" :value="number_format(max(0, $outstandingInvoiceAmount), 2)" hint="Open finance exposure from sent, partial, and overdue invoices." tone="slate" :href="route('finance.invoices.index')" link-label="Open Finance" />
        <x-ui.stat-card label="Overdue Invoices" :value="$overdueInvoices" hint="Invoices that need urgent collection attention." tone="rose" :href="route('finance.invoices.index')" link-label="Collect Now" />
        <x-ui.stat-card label="Today Pool Queue" :value="$todayPoolQueueCount" hint="Dispatch queue requiring same-day operational attention." tone="amber" :href="route('pool.queue')" link-label="Open Pool" />
    </section>

    @if (
        $user?->can('clients.view')
        || $user?->can('vehicles.view')
        || $user?->can('drivers.view')
        || $user?->can('bookings.view')
        || $user?->can('invoices.view')
        || $user?->can('maintenance.view')
        || $user?->can('meeting-logs.view')
    )
        <section class="grid gap-4 lg:grid-cols-2">
            <a href="{{ route('search.index') }}" class="ui-card group px-5 py-5 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-[0_24px_55px_-32px_rgba(29,78,216,0.4)]">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-700">Quick Access</p>
                <div class="mt-3 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold tracking-tight text-slate-950">Workspace Search</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Jump directly to clients, vehicles, drivers, bookings, invoices, and maintenance records during demos or live operations review.</p>
                    </div>
                    <span class="rounded-2xl bg-blue-50 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">Search</span>
                </div>
            </a>

            <a href="{{ route('activity.index') }}" class="ui-card group px-5 py-5 transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-[0_24px_55px_-32px_rgba(5,150,105,0.35)]">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Quick Access</p>
                <div class="mt-3 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold tracking-tight text-slate-950">Recent Activity</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Review the latest booking, finance, CRM, and maintenance movement from one lightweight operational timeline.</p>
                    </div>
                    <span class="rounded-2xl bg-emerald-50 px-3 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Timeline</span>
                </div>
            </a>
        </section>
    @endif

    <section class="grid gap-5 xl:grid-cols-3">
        <x-ui.table-card title="Latest Bookings" description="Recent booking activity with fast drill-down into operational detail.">
            @if ($latestBookings->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No bookings yet" description="New bookings will appear here once the sales or pool flow starts moving." />
                </div>
            @else
                <div class="space-y-3 p-5">
                    @foreach ($latestBookings as $booking)
                        <a href="{{ route('bookings.show', $booking) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/60">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $booking->booking_number }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $booking->client?->name ?? '-' }}</p>
                                </div>
                                <x-ui.status-badge :status="$booking->status" />
                            </div>
                            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">Open booking detail</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>

        <x-ui.table-card title="Latest Payments" description="Fast visibility into recent collections and invoice progress.">
            @if ($latestPayments->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No payments yet" description="Payment activity will appear once the finance flow records collections." />
                </div>
            @else
                <div class="space-y-3 p-5">
                    @foreach ($latestPayments as $payment)
                        <a href="{{ route('finance.invoices.show', $payment->invoice) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-emerald-200 hover:bg-emerald-50/40">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $payment->payment_number }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ number_format($payment->amount, 2) }}</p>
                                </div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $payment->paid_at?->format('Y-m-d') }}</p>
                            </div>
                            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Open invoice detail</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>

        <x-ui.table-card title="Latest Maintenance Logs" description="Recent maintenance movement to support fleet readiness decisions.">
            @if ($latestMaintenanceLogs->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No maintenance logs yet" description="Maintenance entries will show up here once the operation team opens a new case." />
                </div>
            @else
                <div class="space-y-3 p-5">
                    @foreach ($latestMaintenanceLogs as $log)
                        <a href="{{ route('maintenance.show', $log) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $log->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $log->vehicle?->plate_number ?? '-' }}</p>
                                </div>
                                <x-ui.status-badge :status="$log->status" />
                            </div>
                            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.16em] text-blue-700">Open maintenance detail</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>
    </section>
</x-layouts.app>
