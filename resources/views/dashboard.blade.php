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

    $sentInvoices = (float) \App\Models\Invoice::query()->whereIn('status', ['sent', 'partial', 'paid', 'overdue'])->sum('total');
    $collectedAmount = (float) \App\Models\Payment::query()->sum('amount');
    $chartMax = max($sentInvoices, $collectedAmount, 1);
    $sentPercent = min(100, round(($sentInvoices / $chartMax) * 100));
    $collectedPercent = min(100, round(($collectedAmount / $chartMax) * 100));

    $fleetTotal = max($availableVehicles + $vehiclesInPo + $vehiclesInMaintenance, 1);
    $fleetAvailablePercent = round(($availableVehicles / $fleetTotal) * 100);
    $fleetPoPercent = round(($vehiclesInPo / $fleetTotal) * 100);
    $fleetMaintenancePercent = max(0, 100 - $fleetAvailablePercent - $fleetPoPercent);
@endphp

<x-layouts.app :title="'Dashboard'" :header="'Dashboard'">
    <x-breadcrumbs :items="[['label' => 'Dashboard', 'url' => route('dashboard')]]" />

    <x-ui.page-header
        title="Enterprise operations at a glance"
        eyebrow="GM Dashboard"
        description="Pantau performa client, dispatch, finance, dan kesiapan armada dalam tampilan ringkas bergaya corporate Golden Bird CRM."
    >
        <x-slot:actions>
            <div class="ui-card-muted px-4 py-3 text-right">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Current Role</p>
                <p class="mt-1 text-sm font-semibold text-[#042C53]">{{ $user?->getRoleNames()->join(', ') }}</p>
            </div>
        </x-slot:actions>
    </x-ui.page-header>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Total Active Clients" :value="$activeClients" hint="Client aktif yang siap di-follow up tim komersial." tone="blue" :href="route('crm.clients.index', ['status' => 'active'])" link-label="CRM" />
        <x-ui.stat-card label="Active Bookings" :value="$activeBookings" hint="Booking pending, assigned, dan confirmed yang sedang berjalan." tone="emerald" :href="route('bookings.index')" link-label="Pipeline" />
        <x-ui.stat-card label="Pending Bookings" :value="$pendingBookings" hint="Antrean booking yang masih menunggu review atau dispatch." tone="amber" :href="route('bookings.index', ['status' => 'pending'])" link-label="Review" />
        <x-ui.stat-card label="Available Vehicles" :value="$availableVehicles" hint="Armada yang tersedia untuk assignment hari ini." tone="blue" :href="route('fleet.vehicles.index', ['status' => 'available'])" link-label="Fleet" />
        <x-ui.stat-card label="Vehicles In PO" :value="$vehiclesInPo" hint="Unit yang sudah ter-commit untuk booking atau dispatch berikutnya." tone="amber" :href="route('fleet.vehicles.index', ['status' => 'po'])" link-label="PO" />
        <x-ui.stat-card label="Vehicles In Maintenance" :value="$vehiclesInMaintenance" hint="Armada yang sementara keluar dari operasi karena workshop." tone="rose" :href="route('maintenance.index', ['status' => 'in_progress'])" link-label="Maintain" />
        <x-ui.stat-card label="Outstanding Invoices" :value="number_format(max(0, $outstandingInvoiceAmount), 2)" hint="Eksposur invoice open untuk collection dan follow-up." tone="slate" :href="route('finance.invoices.index')" link-label="Finance" />
        <x-ui.stat-card label="Overdue Invoices" :value="$overdueInvoices" hint="Invoice overdue yang butuh perhatian finance segera." tone="rose" :href="route('finance.invoices.index')" link-label="Overdue" />
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.6fr_1fr]">
        <x-ui.table-card title="Revenue Overview" description="Placeholder chart ringkas untuk sent invoice versus collected payment tanpa mengubah dependency chart saat ini." class="ui-subtle-grid">
            <div class="grid gap-4 p-5 lg:grid-cols-[1.3fr_0.9fr]">
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-sm font-medium text-slate-600">
                            <span>Sent Invoice Value</span>
                            <span class="text-[#042C53]">{{ number_format($sentInvoices, 2) }}</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-[#185FA5]" style="width: {{ $sentPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-sm font-medium text-slate-600">
                            <span>Collected Payments</span>
                            <span class="text-[#042C53]">{{ number_format($collectedAmount, 2) }}</span>
                        </div>
                        <div class="mt-2 h-3 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-[#378ADD]" style="width: {{ $collectedPercent }}%"></div>
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="ui-meta-item">
                            <dt>Collection Ratio</dt>
                            <dd>{{ number_format($sentInvoices > 0 ? ($collectedAmount / $sentInvoices) * 100 : 0, 1) }}%</dd>
                        </div>
                        <div class="ui-meta-item">
                            <dt>Today Pool Queue</dt>
                            <dd>{{ $todayPoolQueueCount }}</dd>
                        </div>
                        <div class="ui-meta-item">
                            <dt>Open Invoice Count</dt>
                            <dd>{{ \App\Models\Invoice::query()->whereIn('status', ['sent', 'partial', 'overdue'])->count() }}</dd>
                        </div>
                    </div>
                </div>

                <div class="ui-card-muted flex flex-col justify-between p-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Quick Access</p>
                        <h3 class="mt-2 text-lg font-semibold text-[#042C53]">Workspace Search</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Lompat cepat ke client, booking, invoice, driver, atau maintenance record.</p>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <x-ui.action-button :href="route('search.index')" variant="primary">Open Search</x-ui.action-button>
                        <x-ui.action-button :href="route('activity.index')" variant="ghost">Activity</x-ui.action-button>
                    </div>
                </div>
            </div>
        </x-ui.table-card>

        <x-ui.table-card title="Fleet Status" description="Distribusi armada berdasarkan status operasi saat ini." :data-pool-queue-url="route('pool.queue')">
            <div class="space-y-4 p-5">
                <div class="mx-auto flex h-44 w-44 items-center justify-center rounded-full border-[16px] border-[#EAF2FB] bg-white" style="background: conic-gradient(#185FA5 0 {{ $fleetAvailablePercent }}%, #378ADD {{ $fleetAvailablePercent }}% {{ $fleetAvailablePercent + $fleetPoPercent }}%, #F59E0B {{ $fleetAvailablePercent + $fleetPoPercent }}% 100%);">
                    <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white text-center shadow-sm">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">Fleet</span>
                        <span class="mt-1 text-3xl font-semibold tracking-[-0.04em] text-[#042C53]">{{ $fleetTotal }}</span>
                        <span class="text-xs text-slate-500">Total Unit</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-[#185FA5]"></span>
                            <span class="text-sm font-medium text-slate-600">Available</span>
                        </div>
                        <span class="text-sm font-semibold text-[#042C53]">{{ $availableVehicles }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-[#378ADD]"></span>
                            <span class="text-sm font-medium text-slate-600">PO</span>
                        </div>
                        <span class="text-sm font-semibold text-[#042C53]">{{ $vehiclesInPo }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-sm font-medium text-slate-600">Maintenance</span>
                        </div>
                        <span class="text-sm font-semibold text-[#042C53]">{{ $vehiclesInMaintenance }}</span>
                    </div>
                </div>

                @if ($user?->can('pool.view-all') || $user?->can('pool.view-own'))
                    <div class="pt-1">
                        <x-ui.action-button :href="route('pool.queue')" variant="ghost" class="w-full">Open Pool Queue</x-ui.action-button>
                    </div>
                @endif
            </div>
        </x-ui.table-card>
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
            <a href="{{ route('search.index') }}" class="ui-card ui-kpi-hover group px-5 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#185FA5]">Quick Access</p>
                        <h3 class="mt-2 text-lg font-semibold tracking-[-0.03em] text-[#042C53]">Workspace Search</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan pencarian lintas modul untuk akses cepat saat demo atau review operasional.</p>
                    </div>
                    <span class="rounded-full border border-[#D3E3F6] bg-[#EAF2FB] px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Search</span>
                </div>
            </a>

            <a href="{{ route('activity.index') }}" class="ui-card ui-kpi-hover group px-5 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#185FA5]">Quick Access</p>
                        <h3 class="mt-2 text-lg font-semibold tracking-[-0.03em] text-[#042C53]">Recent Activity</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Pantau pergerakan booking, finance, maintenance, dan CRM dalam satu timeline ringkas.</p>
                    </div>
                    <span class="rounded-full border border-[#D3E3F6] bg-[#EAF2FB] px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Activity</span>
                </div>
            </a>
        </section>
    @endif

    <section class="grid gap-4 xl:grid-cols-3">
        <x-ui.table-card title="Latest Bookings" description="Booking terbaru dengan akses cepat ke detail operasional.">
            @if ($latestBookings->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No bookings yet" description="Booking baru akan muncul di sini setelah sales atau pool mulai bergerak." />
                </div>
            @else
                <div class="space-y-2 p-4">
                    @foreach ($latestBookings as $booking)
                        <a href="{{ route('bookings.show', $booking) }}" class="block rounded-[10px] border border-[#E5E7EB] bg-white px-4 py-3 transition hover:border-[#D3E3F6] hover:bg-[#F9FBFE]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#042C53]">{{ $booking->booking_number }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $booking->client?->name ?? '-' }}</p>
                                </div>
                                <x-ui.status-badge :status="$booking->status" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>

        <x-ui.table-card title="Latest Payments" description="Arus pembayaran terbaru untuk pantauan finance.">
            @if ($latestPayments->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No payments yet" description="Pembayaran akan muncul di sini setelah finance mencatat collection." />
                </div>
            @else
                <div class="space-y-2 p-4">
                    @foreach ($latestPayments as $payment)
                        <a href="{{ route('finance.invoices.show', $payment->invoice) }}" class="block rounded-[10px] border border-[#E5E7EB] bg-white px-4 py-3 transition hover:border-[#D3E3F6] hover:bg-[#F9FBFE]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#042C53]">{{ $payment->payment_number }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ number_format($payment->amount, 2) }}</p>
                                </div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">{{ $payment->paid_at?->format('Y-m-d') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>

        <x-ui.table-card title="Latest Maintenance Logs" description="Pergerakan maintenance terakhir untuk keputusan kesiapan armada.">
            @if ($latestMaintenanceLogs->isEmpty())
                <div class="p-5">
                    <x-ui.empty-state title="No maintenance logs yet" description="Maintenance log akan tampil saat tim operasi membuat case baru." />
                </div>
            @else
                <div class="space-y-2 p-4">
                    @foreach ($latestMaintenanceLogs as $log)
                        <a href="{{ route('maintenance.show', $log) }}" class="block rounded-[10px] border border-[#E5E7EB] bg-white px-4 py-3 transition hover:border-[#D3E3F6] hover:bg-[#F9FBFE]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#042C53]">{{ $log->title }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $log->vehicle?->plate_number ?? '-' }}</p>
                                </div>
                                <x-ui.status-badge :status="$log->status" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>
    </section>
</x-layouts.app>
