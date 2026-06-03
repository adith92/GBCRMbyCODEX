<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'GBCRMbyCODEX') }}</title>

    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @livewireStyles
</head>
<body
    class="app-shell"
    x-data="{
        sidebarOpen: false,
        commandOpen: false,
        commandQuery: '',
        commandItems: {{ \Illuminate\Support\Js::from(collect([
            ['label' => 'Dashboard 🏠', 'url' => route('dashboard'), 'keywords' => 'dashboard home gm kpi overview'],
            ['label' => 'Global Search 🔎', 'url' => route('search.index'), 'keywords' => 'search cmdk global discovery'],
            ['label' => 'Activity Timeline 🕘', 'url' => route('activity.index'), 'keywords' => 'activity timeline history'],
            ['label' => 'CRM Clients 👥', 'url' => route('crm.clients.index'), 'keywords' => 'client crm companies'],
            ['label' => 'Partners & Vendors 🤝', 'url' => route('partners.vendors.index'), 'keywords' => 'partner vendor supplier'],
            ['label' => 'Fleet Vehicles 🚐', 'url' => route('fleet.vehicles.index'), 'keywords' => 'fleet vehicle unit'],
            ['label' => 'Drivers 🧑‍✈️', 'url' => route('drivers.index'), 'keywords' => 'driver crew'],
            ['label' => 'Sales Performance 📈', 'url' => route('sales.index'), 'keywords' => 'sales performance revenue'],
            ['label' => 'Bookings 📋', 'url' => route('bookings.index'), 'keywords' => 'booking trip pipeline'],
            ['label' => 'Pool Queue 🧭', 'url' => route('pool.queue'), 'keywords' => 'pool dispatch queue assignment'],
            ['label' => 'Finance Dashboard 💳', 'url' => route('finance.index'), 'keywords' => 'finance invoice payment po'],
            ['label' => 'Purchase Orders 🧾', 'url' => route('finance.purchase-orders.index'), 'keywords' => 'purchase order po'],
            ['label' => 'Invoices 💰', 'url' => route('finance.invoices.index'), 'keywords' => 'invoice billing collection'],
            ['label' => 'E-Vouchers 🎟️', 'url' => route('finance.e-vouchers.index'), 'keywords' => 'voucher evoucher payment'],
            ['label' => 'Maintenance 🛠️', 'url' => route('maintenance.index'), 'keywords' => 'maintenance workshop service'],
            ['label' => 'Reports 📊', 'url' => route('reports.index'), 'keywords' => 'reports analytics export'],
        ])->values()) }},
        get filteredCommandItems() {
            if (this.commandQuery.trim() === '') {
                return this.commandItems;
            }
            const q = this.commandQuery.toLowerCase();
            return this.commandItems.filter((item) => (item.label + ' ' + item.keywords).toLowerCase().includes(q));
        },
        openCommandPalette() {
            this.commandOpen = true;
            this.$nextTick(() => this.$refs.commandInput?.focus());
        },
        closeCommandPalette() {
            this.commandOpen = false;
            this.commandQuery = '';
        }
    }"
    x-on:keydown.window.prevent.meta.k="openCommandPalette()"
    x-on:keydown.window.prevent.ctrl.k="openCommandPalette()"
    x-on:keydown.escape.window="closeCommandPalette()"
>
@php
    $user = auth()->user();
    $demoEnabled = filter_var((string) env('ENABLE_DEMO_SEED', false), FILTER_VALIDATE_BOOL);
    $salesPerformanceHref = $user && $user->hasRole('sales')
        ? route('sales.performance', $user)
        : route('sales.index');
    $menuGroups = [
        'Command Center' => [
            ['label' => 'Dashboard', 'route' => 'dashboard', 'permissions' => ['dashboard.view'], 'icon' => '🏠', 'patterns' => ['dashboard']],
            ['label' => 'Search', 'route' => 'search.index', 'permissions' => ['clients.view', 'vehicles.view', 'drivers.view', 'bookings.view', 'invoices.view', 'maintenance.view', 'meeting-logs.view'], 'icon' => '🔎'],
            ['label' => 'Activity', 'route' => 'activity.index', 'permissions' => ['clients.view', 'bookings.view', 'invoices.view', 'payments.view', 'maintenance.view', 'meeting-logs.view'], 'icon' => '🕘'],
            ['label' => 'Reports', 'route' => 'reports.index', 'permissions' => ['reports.view'], 'icon' => '📊', 'patterns' => ['reports.*']],
        ],
        'Growth' => [
            ['label' => 'CRM', 'route' => 'crm.index', 'permissions' => ['clients.view', 'meeting-logs.view'], 'icon' => '👥', 'patterns' => ['crm.*']],
            ['label' => 'Partners', 'route' => 'partners.vendors.index', 'permissions' => ['clients.view'], 'icon' => '🤝', 'patterns' => ['partners.*']],
            ['label' => 'Sales', 'route' => 'sales.index', 'permissions' => ['dashboard.view'], 'roles' => ['super-admin', 'gm', 'sales-manager', 'sales'], 'icon' => '📈', 'patterns' => ['sales.index', 'sales.*']],
            ['label' => 'Sales Performance', 'route' => 'sales.index', 'href' => $salesPerformanceHref, 'permissions' => ['dashboard.view'], 'roles' => ['super-admin', 'gm', 'sales-manager', 'sales'], 'icon' => '🎯', 'patterns' => ['sales.performance']],
            ['label' => 'Bookings', 'route' => 'bookings.index', 'permissions' => ['bookings.view'], 'icon' => '📋', 'patterns' => ['bookings.*']],
        ],
        'Operations' => [
            ['label' => 'Fleet', 'route' => 'fleet.index', 'permissions' => ['vehicles.view'], 'icon' => '🚐', 'patterns' => ['fleet.*']],
            ['label' => 'Drivers', 'route' => 'drivers.index', 'permissions' => ['drivers.view'], 'icon' => '🧑‍✈️', 'patterns' => ['drivers.*']],
            ['label' => 'Pool Queue', 'route' => 'pool.queue', 'permissions' => ['pool.view-all', 'pool.view-own'], 'icon' => '🧭', 'patterns' => ['pool.*']],
            ['label' => 'Maintenance', 'route' => 'maintenance.index', 'permissions' => ['maintenance.view'], 'icon' => '🛠️', 'patterns' => ['maintenance.*']],
        ],
        'Finance' => [
            ['label' => 'Finance', 'route' => 'finance.index', 'permissions' => ['purchase-orders.view', 'invoices.view', 'payments.view', 'evouchers.view'], 'icon' => '💳', 'patterns' => ['finance.index', 'finance.*']],
            ['label' => 'Purchase Orders', 'route' => 'finance.purchase-orders.index', 'permissions' => ['purchase-orders.view'], 'icon' => '🧾', 'patterns' => ['finance.purchase-orders.*']],
            ['label' => 'Invoices', 'route' => 'finance.invoices.index', 'permissions' => ['invoices.view'], 'icon' => '💰', 'patterns' => ['finance.invoices.*']],
            ['label' => 'E-Vouchers', 'route' => 'finance.e-vouchers.index', 'permissions' => ['evouchers.view'], 'icon' => '🎟️', 'patterns' => ['finance.e-vouchers.*']],
        ],
        'Secure Admin' => [
            ['label' => 'HR Backend', 'route' => 'admin.hr.drivers', 'permissions' => ['admin.access', 'hr.view'], 'all_required' => true, 'icon' => '🛡️', 'patterns' => ['admin.hr.*']],
        ],
    ];
    $demoUsers = collect(config('rbac.demo_users', []));
@endphp

<div class="min-h-screen lg:flex">
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/45 backdrop-blur-sm lg:hidden" x-on:click="sidebarOpen = false"></div>
    <div x-cloak x-show="commandOpen" class="fixed inset-0 z-[70] bg-slate-950/35 backdrop-blur-md" x-on:click="closeCommandPalette()"></div>

    <aside class="ui-sidebar-panel fixed inset-y-0 left-0 z-50 flex w-[244px] max-w-[88vw] -translate-x-full flex-col transition duration-300 lg:static lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : ''">
        <div class="border-b border-[#E5E7EB] px-4 py-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#D7E7F7] bg-[#F4F8FD] px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-[#185FA5]">🚖 BBCodex Command Hub</div>
                    <h1 class="mt-3 text-[20px] font-semibold tracking-[-0.04em] text-[#042C53]">Golden Bird CRM</h1>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Backend kuat, UX lebih premium, dan workflow demo-friendly untuk tim GM, sales, finance, dan operation.</p>
                </div>
                <button type="button" class="rounded-[9px] border border-[#E5E7EB] p-2 text-slate-500 lg:hidden" x-on:click="sidebarOpen = false">✕</button>
            </div>
        </div>

        <div class="px-4 py-4">
            <div class="ui-card-muted px-3 py-3">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Signed In</p>
                <p class="mt-2 text-sm font-semibold text-[#042C53]">{{ $user?->name }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $user?->getRoleNames()->join(', ') ?: 'No Role' }}</p>
                @if ($demoEnabled)
                    <p class="mt-2 text-[11px] text-[#185FA5]">🧪 Demo mode aktif</p>
                @endif
            </div>
        </div>

        <nav class="flex-1 space-y-4 overflow-y-auto px-3 pb-5">
            @foreach ($menuGroups as $group => $items)
                <div>
                    <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $group }}</p>
                    <div class="space-y-1.5">
                        @foreach ($items as $item)
                            @php
                                $canView = false;
                                if ($user) {
                                    $allRequired = $item['all_required'] ?? false;
                                    $permissionAllowed = $allRequired
                                        ? collect($item['permissions'])->every(fn (string $permission): bool => $user->can($permission))
                                        : collect($item['permissions'])->contains(fn (string $permission): bool => $user->can($permission));
                                    $roleAllowed = empty($item['roles'] ?? [])
                                        || collect($item['roles'])->contains(fn (string $role): bool => $user->hasRole($role));
                                    $canView = $permissionAllowed && $roleAllowed;
                                }
                                $patterns = $item['patterns'] ?? [$item['route'], $item['route'].'.*'];
                                $active = collect($patterns)->contains(fn (string $pattern): bool => request()->routeIs($pattern));
                            @endphp
                            @if ($canView)
                                <a href="{{ $item['href'] ?? route($item['route']) }}" class="group flex items-center gap-3 rounded-[12px] px-3 py-2.5 text-[13px] transition {{ $active ? 'ui-sidebar-active' : 'font-medium text-slate-600 hover:bg-[#EEF4FA] hover:text-[#042C53]' }}">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-[10px] border text-[16px] {{ $active ? 'border-white/20 bg-white/14 shadow-inner' : 'border-[#E5E7EB] bg-white group-hover:border-[#D3E3F6]' }}">{{ $item['icon'] }}</span>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-[#E5E7EB] px-4 py-4">
            <button type="button" x-on:click="openCommandPalette()" class="flex w-full items-center justify-between rounded-[12px] border border-[#D7E7F7] bg-[#F4F8FD] px-3 py-2.5 text-left transition hover:border-[#BFD6EF] hover:bg-white">
                <span>
                    <span class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-[#185FA5]">Command Palette</span>
                    <span class="mt-1 block text-sm font-medium text-[#042C53]">Cari halaman cepat</span>
                </span>
                <span class="rounded-[8px] border border-[#D7E7F7] bg-white px-2 py-1 text-[10px] font-semibold text-slate-500">⌘K</span>
            </button>

            @if ($demoEnabled && $user?->hasRole('super-admin'))
                <form action="{{ route('demo.reset') }}" method="POST" class="mt-3" onsubmit="return confirm('Reset ulang seluruh demo data?')">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center rounded-[12px] border border-amber-200 bg-amber-50 px-3 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">♻️ Reset Demo Seed</button>
                </form>
            @endif
        </div>
    </aside>

    <div class="flex min-h-screen flex-1 flex-col">
        <header class="sticky top-0 z-30 border-b border-[#E5E7EB] bg-white/95 backdrop-blur">
            <div class="mx-auto flex w-full max-w-[1440px] items-center justify-between gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-[10px] border border-[#E5E7EB] bg-white text-slate-600 lg:hidden" x-on:click="sidebarOpen = true">☰</button>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#185FA5]">Golden Bird Corporate CRM ✨</p>
                        <h2 class="mt-1 text-[18px] font-semibold tracking-[-0.03em] text-[#042C53]">{{ $header ?? 'Workspace Overview' }}</h2>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" x-on:click="openCommandPalette()" class="hidden items-center gap-2 rounded-[10px] border border-[#E5E7EB] bg-[#F9FAFB] px-3 py-2 text-sm text-slate-500 transition hover:border-[#D3E3F6] hover:bg-white xl:inline-flex">
                        <span class="font-semibold text-[#185FA5]">⌘K</span>
                        <span>Search command, client, invoice...</span>
                    </button>

                    @if ($demoEnabled)
                        <form action="{{ route('demo.switch-role') }}" method="POST" class="hidden lg:block">
                            @csrf
                            <select name="email" class="ui-select min-w-[190px] text-sm" onchange="this.form.submit()">
                                <option value="">🎭 Demo role switcher</option>
                                @foreach ($demoUsers as $demoUser)
                                    <option value="{{ $demoUser['email'] }}">{{ $demoUser['name'] }} · {{ $demoUser['role'] }}</option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                    <div class="hidden rounded-[10px] border border-[#E5E7EB] bg-white px-3 py-2 text-right sm:block">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500">Current Role</p>
                        <p class="mt-1 text-sm font-semibold text-[#042C53]">{{ $user?->getRoleNames()->join(', ') ?: 'No Role' }}</p>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="no-print">
                        @csrf
                        <x-ui.action-button type="submit" variant="secondary">Logout</x-ui.action-button>
                    </form>
                </div>
            </div>
        </header>

        <main class="app-content flex-1">
            <div class="mx-auto w-full max-w-[1440px] space-y-4 px-4 py-4 sm:px-5 lg:px-7 lg:py-5">
                @if (session('success'))
                    <div class="rounded-[12px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
    <div x-cloak x-show="commandOpen" class="fixed inset-x-0 top-10 z-[80] mx-auto w-[min(760px,92vw)] rounded-[18px] border border-[#D7E7F7] bg-white shadow-2xl">
        <div class="border-b border-[#E5E7EB] px-4 py-4">
            <div class="flex items-center gap-3">
                <span class="text-xl">🔎</span>
                <input x-ref="commandInput" x-model="commandQuery" type="text" class="w-full border-0 bg-transparent text-base text-[#042C53] placeholder:text-slate-400 focus:outline-none focus:ring-0" placeholder="Cari halaman, modul, partner, invoice, booking...">
                <button type="button" class="rounded-[8px] border border-[#E5E7EB] px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-slate-500" x-on:click="closeCommandPalette()">Esc</button>
            </div>
        </div>
        <div class="max-h-[60vh] overflow-y-auto p-3">
            <template x-if="filteredCommandItems.length === 0">
                <div class="rounded-[14px] border border-dashed border-[#D7E7F7] bg-[#F8FBFE] px-4 py-6 text-center text-sm text-slate-500">Belum ada hasil. Coba kata kunci lain ya.</div>
            </template>
            <template x-for="item in filteredCommandItems" :key="item.url">
                <a :href="item.url" class="flex items-center justify-between rounded-[12px] px-3 py-3 transition hover:bg-[#F4F8FD]" x-on:click="closeCommandPalette()">
                    <div>
                        <p class="text-sm font-semibold text-[#042C53]" x-text="item.label"></p>
                        <p class="mt-1 text-xs text-slate-500" x-text="item.keywords"></p>
                    </div>
                    <span class="text-[#185FA5]">↗</span>
                </a>
            </template>
        </div>
    </div>
</div>

@livewireScripts
</body>
</html>
