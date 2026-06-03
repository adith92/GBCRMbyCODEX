@php
    $user = auth()->user();
    $roleNames = $user?->getRoleNames() ?? collect();
    $roleLabel = $roleNames->join(', ') ?: 'workspace';
    $persona = match (true) {
        $roleNames->contains('super-admin') => 'super-admin',
        $roleNames->contains('gm') => 'gm',
        $roleNames->contains('sales-manager') => 'sales-manager',
        $roleNames->contains('sales') => 'sales',
        $roleNames->contains('finance') => 'finance',
        $roleNames->contains('operation') => 'operation',
        $roleNames->contains('head-pool') => 'head-pool',
        $roleNames->contains('pool-staff') => 'pool-staff',
        default => 'general',
    };

    $idr = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
    $activeClients = \App\Models\Client::query()->where('status', 'active')->count();
    $activeBookings = \App\Models\Booking::query()->whereIn('status', ['pending', 'assigned', 'confirmed'])->count();
    $pendingBookings = \App\Models\Booking::query()->where('status', 'pending')->count();
    $availableVehicles = \App\Models\Vehicle::query()->where('status', 'available')->count();
    $vehiclesInPo = \App\Models\Vehicle::query()->where('status', 'po')->count();
    $vehiclesInMaintenance = \App\Models\Vehicle::query()->where('status', 'maintenance')->count();
    $todayPoolQueueCount = \App\Models\Booking::query()->whereDate('start_datetime', today())->whereIn('status', ['pending', 'assigned'])->count();
    $outstandingInvoiceAmount = max(0, (float) \App\Models\Invoice::query()->whereIn('status', ['sent', 'partial', 'overdue'])->sum('total')
        - (float) \App\Models\Invoice::query()->whereIn('status', ['sent', 'partial', 'overdue'])->sum('paid_amount'));
    $overdueInvoices = \App\Models\Invoice::query()->where('status', 'overdue')->count();
    $sentInvoices = (float) \App\Models\Invoice::query()->whereIn('status', ['sent', 'partial', 'paid', 'overdue'])->sum('total');
    $collectedAmount = (float) \App\Models\Payment::query()->sum('amount');
    $collectionRatio = $sentInvoices > 0 ? ($collectedAmount / $sentInvoices) * 100 : 0;

    $latestBookings = \App\Models\Booking::query()->with(['client', 'pool', 'driver'])->latest()->limit(5)->get();
    $latestPayments = \App\Models\Payment::query()->with(['invoice.client'])->latest()->limit(5)->get();
    $latestMaintenanceLogs = \App\Models\MaintenanceLog::query()->with('vehicle')->latest()->limit(5)->get();
    $latestMeetings = \App\Models\MeetingLog::query()->with('client')->latest()->limit(5)->get();
    $salesUsers = \App\Models\User::query()->role(['sales', 'sales-manager'])->orderBy('name')->get();

    $monthLabels = collect(range(5, 1))->map(fn ($back) => now()->subMonths($back)->format('M'))->push(now()->format('M'));
    $monthlySent = [];
    $monthlyCollected = [];
    foreach (range(5, 1) as $back) {
        $date = now()->subMonths($back);
        $monthlySent[] = (float) \App\Models\Invoice::query()->whereYear('issued_at', $date->year)->whereMonth('issued_at', $date->month)->sum('total');
        $monthlyCollected[] = (float) \App\Models\Payment::query()->whereYear('paid_at', $date->year)->whereMonth('paid_at', $date->month)->sum('amount');
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

    $heroMap = [
        'super-admin' => ['emoji' => '🛡️', 'title' => 'Mission control untuk seluruh BBCodex', 'desc' => 'Awasi RBAC, kesehatan workflow, dan kualitas demo environment dari satu command center.'],
        'gm' => ['emoji' => '👔', 'title' => 'Executive command center siap demo', 'desc' => 'Lihat revenue pulse, booking pipeline, armada, dan collection tanpa harus lompat antar modul.'],
        'sales-manager' => ['emoji' => '📣', 'title' => 'Sales command center yang lebih tajam', 'desc' => 'Pantau client growth, follow-up, booking conversion, dan performa tiap sales dalam satu layar padat.'],
        'sales' => ['emoji' => '🤝', 'title' => 'Sales cockpit untuk follow-up cepat', 'desc' => 'Masuk, cari client, cek booking aktif, dan lompat ke performa pribadi tanpa UI admin yang berat.'],
        'finance' => ['emoji' => '💳', 'title' => 'Finance pulse board', 'desc' => 'Fokus ke invoice sent, overdue, collection ratio, dan cash-in terbaru dengan drill-down cepat.'],
        'operation' => ['emoji' => '🛠️', 'title' => 'Operations readiness board', 'desc' => 'Pantau fleet available, maintenance load, dan dispatch readiness untuk eksekusi harian yang lebih rapi.'],
        'head-pool' => ['emoji' => '🧭', 'title' => 'Dispatch command queue', 'desc' => 'Lihat antrean hari ini, assignment, armada siap jalan, dan histori dispatch dengan cepat.'],
        'pool-staff' => ['emoji' => '🚦', 'title' => 'Pool queue focus view', 'desc' => 'Akses ringkas ke antrian dispatch, kendaraan siap, dan booking yang perlu assignment segera.'],
        'general' => ['emoji' => '✨', 'title' => 'Workspace overview', 'desc' => 'Ringkasan cepat lintas modul untuk membantu navigasi dan review operasional.'],
    ];
    $hero = $heroMap[$persona];

    $quickLinks = [
        ['label' => 'Workspace Search', 'emoji' => '🔎', 'desc' => 'Cari client, booking, invoice, partner, atau vehicle dalam sekali tekan.', 'url' => route('search.index')],
        ['label' => 'Recent Activity', 'emoji' => '🕘', 'desc' => 'Lihat semua pergerakan terbaru lintas sales, finance, operation, dan maintenance.', 'url' => route('activity.index')],
        ['label' => 'Reports', 'emoji' => '📊', 'desc' => 'Masuk ke dashboard laporan dan insight yang lebih nyata.', 'url' => route('reports.index')],
    ];

    $roleFocus = match ($persona) {
        'finance' => [
            ['label' => 'Open Invoices', 'value' => \App\Models\Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->count(), 'href' => route('finance.invoices.index')],
            ['label' => 'Collection Ratio', 'value' => number_format($collectionRatio, 1).'%', 'href' => route('finance.index')],
            ['label' => 'Outstanding', 'value' => $idr($outstandingInvoiceAmount), 'href' => route('finance.invoices.index')],
        ],
        'sales' => [
            ['label' => 'My Bookings', 'value' => \App\Models\Booking::where('requested_by', $user?->id)->count(), 'href' => route('sales.performance', $user)],
            ['label' => 'My Meetings', 'value' => \App\Models\MeetingLog::where('user_id', $user?->id)->count(), 'href' => route('sales.performance', $user)],
            ['label' => 'My Revenue', 'value' => $idr((float) \App\Models\Invoice::whereIn('purchase_order_id', function ($query) use ($user) {
                $query->select('id')->from('purchase_orders')->whereIn('booking_id', \App\Models\Booking::where('requested_by', $user?->id)->select('id'));
            })->sum('total')), 'href' => route('sales.performance', $user)],
        ],
        'operation', 'head-pool', 'pool-staff' => [
            ['label' => 'Today Queue', 'value' => $todayPoolQueueCount, 'href' => route('pool.queue')],
            ['label' => 'Available Fleet', 'value' => $availableVehicles, 'href' => route('fleet.vehicles.index', ['status' => 'available'])],
            ['label' => 'Maintenance Open', 'value' => \App\Models\MaintenanceLog::whereIn('status', ['scheduled', 'in_progress'])->count(), 'href' => route('maintenance.index')],
        ],
        default => [
            ['label' => 'Open Queue', 'value' => $todayPoolQueueCount, 'href' => route('pool.queue')],
            ['label' => 'Sales Roster', 'value' => $salesUsers->count(), 'href' => route('sales.index')],
            ['label' => 'Collection', 'value' => $idr($collectedAmount), 'href' => route('finance.index')],
        ],
    };
@endphp

<x-layouts.app :title="'Dashboard'" :header="'Dashboard'">
    <x-breadcrumbs :items="[['label' => 'Dashboard', 'url' => route('dashboard')]]" />

    <section class="ui-command-hero rounded-[18px] px-5 py-5 shadow-[0_22px_48px_rgba(4,44,83,0.24)] lg:px-6">
        <div class="grid gap-5 xl:grid-cols-[1.4fr_0.9fr] xl:items-center">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/90">{{ $hero['emoji'] }} {{ strtoupper($roleLabel) }}</div>
                <h1 class="mt-4 text-3xl font-semibold tracking-[-0.04em] text-white lg:text-[38px]">{{ $hero['title'] }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-white/78 lg:text-[15px]">{{ $hero['desc'] }}</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    @foreach ($quickLinks as $link)
                        <a href="{{ $link['url'] }}" class="rounded-[12px] border border-white/15 bg-white/10 px-4 py-3 text-sm transition hover:bg-white/16">
                            <p class="font-semibold text-white">{{ $link['emoji'] }} {{ $link['label'] }}</p>
                            <p class="mt-1 text-xs text-white/70">{{ $link['desc'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-1">
                @foreach ($roleFocus as $focus)
                    <a href="{{ $focus['href'] }}" class="rounded-[16px] border border-white/15 bg-white/10 px-4 py-4 transition hover:bg-white/16">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-white/70">{{ $focus['label'] }}</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-white">{{ $focus['value'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ui-compact-grid md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-8">
        <x-ui.stat-card label="Total Active Clients" :value="$activeClients" hint="👥 client aktif" tone="blue" :href="route('crm.clients.index', ['status' => 'active'])" link-label="CRM" />
        <x-ui.stat-card label="Active Bookings" :value="$activeBookings" hint="📋 pending-assigned-confirmed" tone="emerald" :href="route('bookings.index')" link-label="Pipeline" />
        <x-ui.stat-card label="Pending" :value="$pendingBookings" hint="⏳ perlu review" tone="amber" :href="route('bookings.index', ['status' => 'pending'])" link-label="Review" />
        <x-ui.stat-card label="Available Vehicles" :value="$availableVehicles" hint="🚐 siap assignment" tone="blue" :href="route('fleet.vehicles.index', ['status' => 'available'])" link-label="Fleet" />
        <x-ui.stat-card label="Fleet In PO" :value="$vehiclesInPo" hint="🧾 committed unit" tone="amber" :href="route('fleet.vehicles.index', ['status' => 'po'])" link-label="PO" />
        <x-ui.stat-card label="Maintenance" :value="$vehiclesInMaintenance" hint="🛠️ workshop load" tone="rose" :href="route('maintenance.index', ['status' => 'in_progress'])" link-label="Maintain" />
        <x-ui.stat-card label="Outstanding" :value="$idr($outstandingInvoiceAmount)" hint="💸 open exposure" tone="slate" :href="route('finance.invoices.index')" link-label="Finance" />
        <x-ui.stat-card label="Overdue Invoices" :value="$overdueInvoices" hint="🚨 follow-up collection" tone="rose" :href="route('finance.invoices.index', ['status' => 'overdue'])" link-label="Overdue" />
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.5fr_1fr]">
        <x-ui.table-card title="Revenue Command Chart" description="Trend monthly sent invoice vs collected payment dalam view finance yang lebih hidup.">
            <div class="grid gap-4 p-4 lg:grid-cols-[1.5fr_0.9fr]">
                <a href="{{ route('finance.invoices.index') }}" class="block rounded-[14px] border border-[#E5E7EB] bg-[#FBFDFF] p-3 transition hover:border-[#D3E3F6] hover:bg-white">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">📈 Revenue Trend Monthly</p>
                            <p class="mt-1 text-[13px] text-slate-500">Klik untuk masuk ke invoice workspace.</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] text-slate-500">Collection ratio</p>
                            <p class="text-sm font-semibold text-[#042C53]">{{ number_format($collectionRatio, 1) }}%</p>
                        </div>
                    </div>
                    <svg viewBox="0 0 360 190" class="mt-3 h-[190px] w-full">
                        @foreach ([38, 71, 104, 137, 170] as $y)
                            <line x1="24" y1="{{ $y }}" x2="336" y2="{{ $y }}" stroke="#E5E7EB" stroke-dasharray="4 6" />
                        @endforeach
                        @foreach ($monthLabels as $index => $month)
                            @php $x = 24 + ($index * (312 / max($monthLabels->count() - 1, 1))); @endphp
                            <text x="{{ $x }}" y="184" text-anchor="middle" class="fill-slate-400 text-[10px]">{{ $month }}</text>
                        @endforeach
                        <polyline points="{{ $sentPoints }}" fill="none" stroke="#185FA5" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        <polyline points="{{ $collectedPoints }}" fill="none" stroke="#22C55E" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="mt-3 flex flex-wrap gap-3 text-[12px] text-slate-600">
                        <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#185FA5]"></span>Sent invoice</span>
                        <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#22C55E]"></span>Collected</span>
                    </div>
                </a>

                <div class="space-y-3">
                    <a href="{{ route('finance.invoices.index') }}" class="ui-card-muted block p-3 transition hover:border-[#D3E3F6] hover:bg-white">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">🧾 Sent Invoice</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-[#042C53]">{{ $idr($sentInvoices) }}</p>
                    </a>
                    <a href="{{ route('finance.invoices.index', ['status' => 'paid']) }}" class="ui-card-muted block p-3 transition hover:border-[#D3E3F6] hover:bg-white">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">✅ Collected</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-[#042C53]">{{ $idr($collectedAmount) }}</p>
                    </a>
                    <a href="{{ route('finance.invoices.index') }}" class="ui-card-muted block p-3 transition hover:border-[#D3E3F6] hover:bg-white">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">💸 Outstanding</p>
                        <p class="mt-2 text-2xl font-semibold tracking-[-0.03em] text-[#042C53]">{{ $idr($outstandingInvoiceAmount) }}</p>
                    </a>
                </div>
            </div>
        </x-ui.table-card>

        <x-ui.table-card title="Fleet Readiness" description="Visual cepat untuk pembagian fleet availability.">
            <div class="space-y-3 p-4">
                <div class="mx-auto flex h-44 w-44 items-center justify-center rounded-full border-[16px] border-[#EAF2FB] bg-white" style="background: conic-gradient(#185FA5 0 {{ $fleetAvailablePercent }}%, #378ADD {{ $fleetAvailablePercent }}% {{ $fleetAvailablePercent + $fleetPoPercent }}%, #F59E0B {{ $fleetAvailablePercent + $fleetPoPercent }}% 100%);">
                    <div class="flex h-28 w-28 flex-col items-center justify-center rounded-full bg-white text-center shadow-sm">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">🚐 Fleet</span>
                        <span class="mt-1 text-3xl font-semibold tracking-[-0.04em] text-[#042C53]">{{ $fleetTotal }}</span>
                        <span class="text-xs text-slate-500">Total unit</span>
                    </div>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('fleet.vehicles.index', ['status' => 'available']) }}" class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2 transition hover:border-[#D3E3F6]">
                        <span class="text-sm font-medium text-slate-600">🔵 Available</span><span class="text-sm font-semibold text-[#042C53]">{{ $availableVehicles }}</span>
                    </a>
                    <a href="{{ route('fleet.vehicles.index', ['status' => 'po']) }}" class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2 transition hover:border-[#D3E3F6]">
                        <span class="text-sm font-medium text-slate-600">🧾 In PO</span><span class="text-sm font-semibold text-[#042C53]">{{ $vehiclesInPo }}</span>
                    </a>
                    <a href="{{ route('maintenance.index', ['status' => 'in_progress']) }}" class="flex items-center justify-between rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2 transition hover:border-[#D3E3F6]">
                        <span class="text-sm font-medium text-slate-600">🛠️ Maintenance</span><span class="text-sm font-semibold text-[#042C53]">{{ $vehiclesInMaintenance }}</span>
                    </a>
                </div>
            </div>
        </x-ui.table-card>
    </section>

    <section class="grid gap-4 xl:grid-cols-3">
        <x-ui.table-card title="Latest Bookings" description="Booking terbaru dengan shortcut ke client, driver, dan pool.">
            @if ($latestBookings->isEmpty())
                <div class="p-5"><x-ui.empty-state title="No bookings yet" description="Booking baru akan muncul di sini begitu flow mulai bergerak." /></div>
            @else
                <div class="space-y-3 p-4">
                    @foreach ($latestBookings as $booking)
                        <a href="{{ route('bookings.show', $booking) }}" class="block rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3 transition hover:border-[#D3E3F6] hover:bg-[#FBFDFF]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#042C53]">{{ $booking->booking_number }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $booking->client?->name ?? '-' }} · {{ $booking->pool?->name ?? '-' }}</p>
                                </div>
                                <x-ui.status-badge :status="$booking->status" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>

        <x-ui.table-card title="Latest Payments" description="Pergerakan collection terbaru untuk finance pulse board.">
            @if ($latestPayments->isEmpty())
                <div class="p-5"><x-ui.empty-state title="No payments yet" description="Payment history akan muncul di sini setelah collection masuk." /></div>
            @else
                <div class="space-y-3 p-4">
                    @foreach ($latestPayments as $payment)
                        <a href="{{ $payment->invoice ? route('finance.invoices.show', $payment->invoice) : route('finance.index') }}" class="block rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3 transition hover:border-[#D3E3F6] hover:bg-[#FBFDFF]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#042C53]">{{ $payment->payment_number }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $payment->invoice?->client?->name ?? 'No client' }} · {{ $idr($payment->amount) }}</p>
                                </div>
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-emerald-700">{{ $payment->method }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>

        <x-ui.table-card title="Latest Maintenance" description="Case maintenance terbaru untuk operation dan workshop visibility.">
            @if ($latestMaintenanceLogs->isEmpty())
                <div class="p-5"><x-ui.empty-state title="No maintenance logs yet" description="Log maintenance akan tampil di sini setelah armada masuk proses service." /></div>
            @else
                <div class="space-y-3 p-4">
                    @foreach ($latestMaintenanceLogs as $log)
                        <a href="{{ route('maintenance.show', $log) }}" class="block rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3 transition hover:border-[#D3E3F6] hover:bg-[#FBFDFF]">
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

    @if ($persona === 'sales' || $persona === 'sales-manager' || $persona === 'gm' || $persona === 'super-admin')
        <x-ui.table-card title="Sales pulse" description="Roster singkat sales dan akses cepat ke halaman performa.">
            <div class="grid gap-3 p-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($salesUsers->take(4) as $salesUser)
                    <a href="{{ route('sales.performance', $salesUser) }}" class="ui-card-muted p-4 transition hover:border-[#D3E3F6] hover:bg-white">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">🧑‍💼 Sales</p>
                        <p class="mt-2 text-base font-semibold text-[#042C53]">{{ $salesUser->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $salesUser->email }}</p>
                    </a>
                @endforeach
            </div>
        </x-ui.table-card>
    @endif

    @if ($persona === 'sales' || $persona === 'sales-manager')
        <x-ui.table-card title="Latest meeting logs" description="Supaya follow-up sales terasa dekat dengan dashboard utama.">
            @if ($latestMeetings->isEmpty())
                <div class="p-5"><x-ui.empty-state title="No meeting logs yet" description="Meeting log terbaru akan tampil di sini setelah tim sales melakukan follow-up." /></div>
            @else
                <div class="space-y-3 p-4">
                    @foreach ($latestMeetings as $meeting)
                        <a href="{{ $meeting->client ? route('crm.clients.show', $meeting->client) : route('crm.clients.index') }}" class="block rounded-[14px] border border-[#E5E7EB] bg-white px-4 py-3 transition hover:border-[#D3E3F6] hover:bg-[#FBFDFF]">
                            <p class="font-semibold text-[#042C53]">{{ $meeting->title }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $meeting->client?->name ?? '-' }} · {{ strtoupper($meeting->outcome) }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.table-card>
    @endif
</x-layouts.app>
