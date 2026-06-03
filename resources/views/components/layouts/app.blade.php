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
<body class="app-shell" x-data="{ sidebarOpen: false }">
@php
    $user = auth()->user();
    $salesPerformanceHref = $user && $user->hasRole('sales')
        ? route('sales.performance', $user)
        : route('sales.index');
    $menuItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'permissions' => ['dashboard.view'], 'icon' => 'DB', 'patterns' => ['dashboard']],
        ['label' => 'Search', 'route' => 'search.index', 'permissions' => ['clients.view', 'vehicles.view', 'drivers.view', 'bookings.view', 'invoices.view', 'maintenance.view', 'meeting-logs.view'], 'icon' => 'SR'],
        ['label' => 'Activity', 'route' => 'activity.index', 'permissions' => ['clients.view', 'bookings.view', 'invoices.view', 'payments.view', 'maintenance.view', 'meeting-logs.view'], 'icon' => 'AC'],
        ['label' => 'CRM', 'route' => 'crm.index', 'permissions' => ['clients.view', 'meeting-logs.view'], 'icon' => 'CR', 'patterns' => ['crm.*']],
        ['label' => 'Fleet', 'route' => 'fleet.index', 'permissions' => ['vehicles.view'], 'icon' => 'FL', 'patterns' => ['fleet.*']],
        ['label' => 'Drivers', 'route' => 'drivers.index', 'permissions' => ['drivers.view'], 'icon' => 'DR', 'patterns' => ['drivers.*']],
        ['label' => 'Sales', 'route' => 'sales.index', 'permissions' => ['dashboard.view'], 'roles' => ['super-admin', 'gm', 'sales-manager', 'sales'], 'icon' => 'SL', 'patterns' => ['sales.*']],
        ['label' => 'Sales Performance', 'route' => 'sales.index', 'href' => $salesPerformanceHref, 'permissions' => ['dashboard.view'], 'roles' => ['super-admin', 'gm', 'sales-manager', 'sales'], 'icon' => 'SP', 'patterns' => ['sales.performance']],
        ['label' => 'Bookings', 'route' => 'bookings.index', 'permissions' => ['bookings.view'], 'icon' => 'BK', 'patterns' => ['bookings.*']],
        ['label' => 'Pool Queue', 'route' => 'pool.queue', 'permissions' => ['pool.view-all', 'pool.view-own'], 'icon' => 'PQ', 'patterns' => ['pool.*']],
        ['label' => 'Finance', 'route' => 'finance.index', 'permissions' => ['purchase-orders.view', 'invoices.view', 'payments.view', 'evouchers.view'], 'icon' => 'FN', 'patterns' => ['finance.*']],
        ['label' => 'Purchase Orders', 'route' => 'finance.purchase-orders.index', 'permissions' => ['purchase-orders.view'], 'icon' => 'PO'],
        ['label' => 'Invoices', 'route' => 'finance.invoices.index', 'permissions' => ['invoices.view'], 'icon' => 'IV'],
        ['label' => 'E-Vouchers', 'route' => 'finance.e-vouchers.index', 'permissions' => ['evouchers.view'], 'icon' => 'EV'],
        ['label' => 'Maintenance', 'route' => 'maintenance.index', 'permissions' => ['maintenance.view'], 'icon' => 'MT', 'patterns' => ['maintenance.*']],
        ['label' => 'Reports', 'route' => 'reports.index', 'permissions' => ['reports.view'], 'icon' => 'RP'],
        ['label' => 'HR Backend', 'route' => 'admin.hr.drivers', 'permissions' => ['admin.access', 'hr.view'], 'all_required' => true, 'icon' => 'HR'],
    ];
@endphp

<div class="min-h-screen lg:flex">
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/30 backdrop-blur-sm lg:hidden" x-on:click="sidebarOpen = false"></div>

    <aside class="ui-sidebar-panel fixed inset-y-0 left-0 z-50 flex w-[220px] max-w-[86vw] -translate-x-full flex-col transition duration-300 lg:static lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : ''">
        <div class="border-b border-[#E5E7EB] px-4 py-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#185FA5]">GBCRMbyCODEX</p>
                    <h1 class="mt-2 text-[18px] font-semibold tracking-[-0.03em] text-[#042C53]">Golden Bird CRM</h1>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Corporate CRM, fleet, dispatch, finance, and maintenance workspace.</p>
                </div>
                <button type="button" class="rounded-[9px] border border-[#E5E7EB] p-2 text-slate-500 lg:hidden" x-on:click="sidebarOpen = false">X</button>
            </div>
        </div>

        <div class="px-4 py-4">
            <div class="ui-card-muted px-3 py-3">
                <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-500">Signed In</p>
                <p class="mt-2 text-sm font-semibold text-[#042C53]">{{ $user?->name }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $user?->getRoleNames()->join(', ') ?: 'No Role' }}</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1.5 overflow-y-auto px-3 pb-5">
            @foreach ($menuItems as $item)
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
                    <a href="{{ $item['href'] ?? route($item['route']) }}" class="group flex items-center gap-3 rounded-[10px] px-3 py-2.5 text-[13px] transition {{ $active ? 'ui-sidebar-active' : 'font-medium text-slate-600 hover:bg-[#EEF4FA] hover:text-[#042C53]' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-[9px] border text-[10px] font-semibold {{ $active ? 'border-white/20 bg-white/14 text-white shadow-inner' : 'border-[#E5E7EB] bg-white text-[#185FA5] group-hover:border-[#D3E3F6]' }}">{{ $item['icon'] }}</span>
                        <span class="truncate">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex min-h-screen flex-1 flex-col">
        <header class="sticky top-0 z-30 border-b border-[#E5E7EB] bg-white/95 backdrop-blur">
            <div class="mx-auto flex w-full max-w-[1440px] items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-[9px] border border-[#E5E7EB] bg-white text-slate-600 lg:hidden" x-on:click="sidebarOpen = true">
                        <span class="text-lg">=</span>
                    </button>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#185FA5]">Golden Bird Corporate CRM</p>
                        <h2 class="mt-1 text-[18px] font-semibold tracking-[-0.03em] text-[#042C53]">{{ $header ?? 'Workspace Overview' }}</h2>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if ($user && (
                        $user->can('clients.view')
                        || $user->can('vehicles.view')
                        || $user->can('drivers.view')
                        || $user->can('bookings.view')
                        || $user->can('invoices.view')
                        || $user->can('maintenance.view')
                        || $user->can('meeting-logs.view')
                    ))
                        <form action="{{ route('search.index') }}" method="GET" class="hidden xl:block no-print">
                            <label for="global-search" class="sr-only">Search workspace</label>
                            <div class="flex items-center gap-2 rounded-[9px] border border-[#E5E7EB] bg-[#F9FAFB] px-3 py-2">
                                <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-[#185FA5]">Search</span>
                                <input id="global-search" name="q" type="text" value="{{ request('q') }}" placeholder="Client, booking, invoice..." class="w-52 border-0 bg-transparent px-0 py-0 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0">
                            </div>
                        </form>
                    @endif

                    <div class="hidden rounded-[9px] border border-[#E5E7EB] bg-white px-3 py-2 text-right sm:block">
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
</div>

@livewireScripts
</body>
</html>
