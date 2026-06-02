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
    $menuItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'permissions' => ['dashboard.view'], 'icon' => 'DB'],
        ['label' => 'Search', 'route' => 'search.index', 'permissions' => ['clients.view', 'vehicles.view', 'drivers.view', 'bookings.view', 'invoices.view', 'maintenance.view', 'meeting-logs.view'], 'icon' => 'SR'],
        ['label' => 'Activity', 'route' => 'activity.index', 'permissions' => ['clients.view', 'bookings.view', 'invoices.view', 'payments.view', 'maintenance.view', 'meeting-logs.view'], 'icon' => 'AC'],
        ['label' => 'CRM', 'route' => 'crm.index', 'permissions' => ['clients.view', 'meeting-logs.view'], 'icon' => 'CR'],
        ['label' => 'Fleet', 'route' => 'fleet.index', 'permissions' => ['vehicles.view'], 'icon' => 'FL'],
        ['label' => 'Drivers', 'route' => 'drivers.index', 'permissions' => ['drivers.view'], 'icon' => 'DR'],
        ['label' => 'Bookings', 'route' => 'bookings.index', 'permissions' => ['bookings.view'], 'icon' => 'BK'],
        ['label' => 'Pool Queue', 'route' => 'pool.queue', 'permissions' => ['pool.view-all', 'pool.view-own'], 'icon' => 'PQ'],
        ['label' => 'Finance', 'route' => 'finance.index', 'permissions' => ['purchase-orders.view', 'invoices.view', 'payments.view', 'evouchers.view'], 'icon' => 'FN'],
        ['label' => 'Purchase Orders', 'route' => 'finance.purchase-orders.index', 'permissions' => ['purchase-orders.view'], 'icon' => 'PO'],
        ['label' => 'Invoices', 'route' => 'finance.invoices.index', 'permissions' => ['invoices.view'], 'icon' => 'IV'],
        ['label' => 'E-Vouchers', 'route' => 'finance.e-vouchers.index', 'permissions' => ['evouchers.view'], 'icon' => 'EV'],
        ['label' => 'Maintenance', 'route' => 'maintenance.index', 'permissions' => ['maintenance.view'], 'icon' => 'MT'],
        ['label' => 'Reports', 'route' => 'reports.index', 'permissions' => ['reports.view'], 'icon' => 'RP'],
        ['label' => 'HR Backend', 'route' => 'admin.hr.drivers', 'permissions' => ['admin.access', 'hr.view'], 'all_required' => true, 'icon' => 'HR'],
    ];
@endphp

<div class="min-h-screen lg:flex">
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/35 backdrop-blur-sm lg:hidden" x-on:click="sidebarOpen = false"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex w-80 max-w-[88vw] -translate-x-full flex-col border-r border-slate-200/80 bg-white/95 shadow-2xl transition duration-300 lg:static lg:z-auto lg:w-80 lg:translate-x-0 lg:bg-white lg:shadow-none" :class="sidebarOpen ? 'translate-x-0' : ''">
        <div class="border-b border-slate-200/80 px-6 py-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-700">GBCRMbyCODEX</p>
                    <h1 class="mt-2 text-xl font-semibold tracking-tight text-slate-950">Enterprise Demo Console</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-500">CRM, dispatch, finance, maintenance, and restricted admin workflows in one workspace.</p>
                </div>
                <button type="button" class="rounded-xl border border-slate-200 p-2 text-slate-500 lg:hidden" x-on:click="sidebarOpen = false">X</button>
            </div>
        </div>

        <div class="px-4 py-5">
            <div class="ui-card-muted px-4 py-3">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Signed In</p>
                <p class="mt-2 text-sm font-semibold text-slate-900">{{ $user?->name }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $user?->getRoleNames()->join(', ') ?: 'No Role' }}</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 pb-6">
            @foreach ($menuItems as $item)
                @php
                    $canView = false;
                    if ($user) {
                        $allRequired = $item['all_required'] ?? false;
                        $canView = $allRequired
                            ? collect($item['permissions'])->every(fn (string $permission): bool => $user->can($permission))
                            : collect($item['permissions'])->contains(fn (string $permission): bool => $user->can($permission));
                    }
                    $active = request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*');
                @endphp
                @if ($canView)
                    <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium transition {{ $active ? 'bg-blue-700 text-white shadow-lg shadow-blue-700/20' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl text-[11px] font-bold {{ $active ? 'bg-white/15 text-white' : 'bg-blue-50 text-blue-700 group-hover:bg-white' }}">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </aside>

    <div class="flex min-h-screen flex-1 flex-col">
        <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/85 backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm lg:hidden" x-on:click="sidebarOpen = true">
                        <span class="text-lg">=</span>
                    </button>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500">Bluebird-inspired operations workspace</p>
                        <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-950">{{ $header ?? 'Workspace Overview' }}</h2>
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
                        <form action="{{ route('search.index') }}" method="GET" class="hidden xl:block">
                            <label for="global-search" class="sr-only">Search workspace</label>
                            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Search</span>
                                <input id="global-search" name="q" type="text" value="{{ request('q') }}" placeholder="Client, booking, invoice..." class="w-56 border-0 bg-transparent px-0 py-0 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-0">
                            </div>
                        </form>
                    @endif
                    <div class="hidden rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-right sm:block">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Current Role</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $user?->getRoleNames()->join(', ') ?: 'No Role' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <x-ui.action-button type="submit" variant="secondary">Logout</x-ui.action-button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="mx-auto w-full max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                @if (session('success'))
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
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
