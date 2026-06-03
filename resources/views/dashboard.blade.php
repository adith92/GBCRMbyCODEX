@php
    $user = auth()->user();
    $idr = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
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
    $openInvoiceCount = \App\Models\Invoice::query()->whereIn('status', ['sent', 'partial', 'overdue'])->count();
    $collectionRatio = $sentInvoices > 0 ? ($collectedAmount / $sentInvoices) * 100 : 0;

    $monthLabels = collect(range(5, 1))->map(fn ($back) => now()->subMonths($back)->format('M'));
    $monthLabels = $monthLabels->push(now()->format('M'));
    $monthlySent = [];
    $monthlyCollected = [];
    foreach (range(5, 1) as $back) {
        $date = now()->subMonths($back);
        $monthlySent[] = (float) \App\Models\Invoice::query()
            ->whereYear('issued_at', $date->year)
            ->whereMonth('issued_at', $date->month)
            ->sum('total');
        $monthlyCollected[] = (float) \App\Models\Payment::query()
            ->whereYear('paid_at', $date->year)
            ->whereMonth('paid_at', $date->month)
            ->sum('amount');
    }
    $monthlySent[] = (float) \App\Models\Invoice::query()->whereYear('issued_at', now()->year)->whereMonth('issued_at', now()->month)->sum('total');
    $monthlyCollected[] = (float) \App\Models\Payment::query()->whereYear('paid_at', now()->year)->whereMonth('paid_at', now()->month)->sum('amount');
    $chartMax = max(array_merge($monthlySent, $monthlyCollected, [1]));

    $plotPoints = function (array $values) use ($chartMax): string {
        $count = max(count($values) - 1, 1);
        return collect($values)->map(function ($value, $index) use ($chartMax, $count) {
            $x = 24 + ($index * (312 / $count));
            $y = 170 - (($value / $chartMax) * 132);
            return round($x, 2).','.round($y, 2);
        })->implode(' ');
    };
    $sentPoints = $plotPoints($monthlySent);
    $collectedPoints = $plotPoints($monthlyCollected);

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
        description="Pantau client, dispatch, finance, dan kesiapan armada dalam layout operator-friendly yang lebih padat."
    >
        <x-slot:actions>
            <div class="ui-card-muted px-4 py-3 text-right">
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Current Role</p>
                <p class="mt-1 text-sm font-semibold text-[#042C53]">{{ $user?->getRoleNames()->join(', ') }}</p>
            </div>
        </x-slot:actions>
    </x-ui.page-header>

    <section class="ui-compact-grid md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-8">
        <x-ui.stat-card label="Active Clients" :value="$activeClients" hint="Client aktif siap follow-up." tone="blue" :href="route('crm.clients.index', ['status' => 'active'])" link-label="CRM" />
        <x-ui.stat-card label="Active Bookings" :value="$activeBookings" hint="Pending, assigned, confirmed." tone="emerald" :href="route('bookings.index')" link-label="Pipeline" />
        <x-ui.stat-card label="Pending" :value="$pendingBookings" hint="Perlu review atau dispatch." tone="amber" :href="route('bookings.index', ['status' => 'pending'])" link-label="Review" />
        <x-ui.stat-card label="Available Fleet" :value="$availableVehicles" hint="Siap assignment." tone="blue" :href="route('fleet.vehicles.index', ['status' => 'available'])" link-label="Fleet" />
        <x-ui.stat-card label="Fleet In PO" :value="$vehiclesInPo" hint="Sudah ter-commit." tone="amber" :href="route('fleet.vehicles.index', ['status' => 'po'])" link-label="PO" />
        <x-ui.stat-card label="Maintenance" :value="$vehiclesInMaintenance" hint="Sedang workshop." tone="rose" :href="route('maintenance.index', ['status' => 'in_progress'])" link-label="Maintain" />
        <x-ui.stat-card label="Outstanding" :value="$idr(max(0, $outstandingInvoiceAmount))" hint="Invoice open tersisa." tone="slate" :href="route('finance.invoices.index')" link-label="Finance" />
        <x-ui.stat-card label="Overdue" :value="$overdueInvoices" hint="Butuh follow-up collection." tone="rose" :href="route('finance.invoices.index', ['status' => 'overdue'])" link-label="Overdue" />
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.6fr_1fr]">
        <x-ui.table-card title="Revenue Overview" description="Monthly sent invoice versus collected payment dengan summary collection yang lebih padat." class="ui-subtle-grid">
            <div class="grid gap-4 p-4 lg:grid-cols-[1.45fr_0.95fr]">
                <div class="space-y-3">
                    <a href="{{ route('finance.invoices.index') }}" class="block rounded-[12px] border border-[#E5E7EB] bg-white/80 p-3 transition hover:border-[#D3E3F6] hover:bg-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Revenue Trend Monthly</p>
                                <p class="mt-1 text-[13px] text-slate-500">Klik untuk buka invoice pipeline finance.</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[11px] text-slate-500">Sent</p>
                                <p class="text-sm font-semibold text-[#042C53]">{{ $idr($sentInvoices) }}</p>
                            </div>
                        </div>
                        <svg viewBox="0 0 360 190" class="mt-3 h-[190px] w-full">
                            <defs>
                                <linearGradient id="sentGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#185FA5" stop-opacity="0.22" />
                                    <stop offset="100%" stop-color="#185FA5" stop-opacity="0.04" />
                                </linearGradient>
                            </defs>
                            @foreach ([38, 71, 104, 137, 170] as $y)
                                <line x1="24" y1="{{ $y }}" x2="336" y2="{{ $y }}" stroke="#E5E7EB" stroke-dasharray="4 6" />
                            @endforeach
                            @foreach ($monthLabels as $index => $month)
                                @php $x = 24 + ($index * (312 / max($monthLabels->count() - 1, 1))); @endphp
                                <text x="{{ $x }}" y="184" text-anchor="middle" class="fill-slate-400 text-[10px]">{{ $month }}</text>
                            @endforeach
                            <polyline points="{{ $sentPoints }}" fill="none" stroke="#185FA5" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            <polyline points="{{ $collectedPoints }}" fill="none" stroke="#378ADD" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="0" />
                        </svg>
                        <div class="mt-3 flex flex-wrap gap-3 text-[12px] text-slate-600">
                            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#185FA5]"></span>Sent invoice</span>
                            <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#378ADD]"></span>Collected</span>
                        </div>
                    </a>

                    <div class="grid gap-3 sm:grid-cols-4">
                        <a href="{{ route('finance.invoices.index') }}" class="ui-meta-item transition hover:border-[#D3E3F6] hover:bg-[#F9FBFE]">
                            <dt>Sent Invoice</dt>
                            <dd>{{ $idr($sentInvoices) }}</dd>
                        </a>
                        <a href="{{ route('finance.invoices.index', ['status' => 'paid']) }}" class="ui-meta-item transition hover:border-[#D3E3F6] hover:bg-[#F9FBFE]">
                            <dt>Collected</dt>
                            <dd>{{ $idr($collectedAmount) }}</dd>
                        </a>
                        <a href="{{ route('finance.invoices.index') }}" class="ui-meta-item transition hover:border-[#D3E3F6] hover:bg-[#F9FBFE]">
                            <dt>Outstanding</dt>
                            <dd>{{ $idr(max(0, $outstandingInvoiceAmount)) }}</dd>
                        </a>
                        <a href="{{ route('finance.invoices.index') }}" class="ui-meta-item transition hover:border-[#D3E3F6] hover:bg-[#F9FBFE]">
                            <dt>Collection Ratio</dt>
                            <dd>{{ number_format($collectionRatio, 1) }}%</dd>
                        </a>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="ui-card-muted p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Collection Pulse</p>
                        <div class="mt-3 space-y-3">
                            <div class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2">
                                <span class="text-[13px] font-medium text-slate-600">Open invoice count</span>
                                <span class="text-sm font-semibold text-[#042C53]">{{ $openInvoiceCount }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2">
                                <span class="text-[13px] font-medium text-slate-600">Overdue invoices</span>
                                <span class="text-sm font-semibold text-rose-600">{{ $overdueInvoices }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2">
                                <span class="text-[13px] font-medium text-slate-600">Today pool queue</span>
                                <span class="text-sm font-semibold text-[#042C53]">{{ $todayPoolQueueCount }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-muted p-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Quick Access</p>
                        <div class="mt-3 grid gap-2">
                            <x-ui.action-button :href="route('search.index')" variant="primary" class="justify-center">Open Search</x-ui.action-button>
                            <x-ui.action-button :href="route('activity.index')" variant="ghost" class="justify-center">Recent Activity</x-ui.action-button>
                            <x-ui.action-button :href="route('sales.index')" variant="secondary" class="justify-center">Sales Performance</x-ui.action-button>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.table-card>

        <x-ui.table-card title="Fleet Status" description="Distribusi armada berdasarkan status operasi saat ini." :data-pool-queue-url="route('pool.queue')">
            <div class="space-y-3 p-4">
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
        <section class="grid gap-3 lg:grid-cols-2">
            <a href="{{ route('search.index') }}" class="ui-card ui-kpi-hover group px-4 py-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#185FA5]">Quick Access</p>
                        <h3 class="mt-2 text-lg font-semibold tracking-[-0.03em] text-[#042C53]">Workspace Search</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan pencarian lintas modul untuk akses cepat saat demo atau review operasional.</p>
                    </div>
                    <span class="rounded-full border border-[#D3E3F6] bg-[#EAF2FB] px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Search</span>
                </div>
            </a>

            <a href="{{ route('activity.index') }}" class="ui-card ui-kpi-hover group px-4 py-4">
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

    <section class="grid gap-3 xl:grid-cols-3">
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
