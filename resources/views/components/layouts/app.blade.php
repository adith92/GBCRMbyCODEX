<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'BlueERP') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<div class="flex min-h-screen">
    <aside class="hidden w-72 border-r border-slate-200 bg-white lg:block">
        <div class="border-b border-slate-200 px-6 py-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">BlueERP</p>
            <h1 class="mt-1 text-xl font-semibold text-slate-900">Navigation</h1>
        </div>

        @php
            $menuItems = [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'permissions' => ['dashboard.view']],
                ['label' => 'CRM', 'route' => 'crm.index', 'permissions' => ['clients.view', 'meeting-logs.view']],
                ['label' => 'Fleet', 'route' => 'fleet.index', 'permissions' => ['vehicles.view']],
                ['label' => 'Drivers', 'route' => 'drivers.index', 'permissions' => ['drivers.view']],
                ['label' => 'Pool', 'route' => 'pool.index', 'permissions' => ['pool.view-all', 'pool.view-own']],
                ['label' => 'Bookings', 'route' => 'bookings.index', 'permissions' => ['bookings.view']],
                ['label' => 'Finance', 'route' => 'finance.index', 'permissions' => ['purchase-orders.view', 'invoices.view', 'payments.view', 'evouchers.view']],
                ['label' => 'Maintenance', 'route' => 'maintenance.index', 'permissions' => ['maintenance.view']],
                ['label' => 'Reports', 'route' => 'reports.index', 'permissions' => ['reports.view']],
                ['label' => 'HR (Backend)', 'route' => 'admin.hr.index', 'permissions' => ['admin.access', 'hr.view'], 'all_required' => true],
            ];
        @endphp

        <nav class="px-4 py-4">
            <ul class="space-y-1 text-sm">
                @foreach ($menuItems as $item)
                    @php
                        $canView = false;
                        if (auth()->check()) {
                            $allRequired = $item['all_required'] ?? false;
                            if ($allRequired) {
                                $canView = collect($item['permissions'])->every(fn (string $permission): bool => auth()->user()->can($permission));
                            } else {
                                $canView = collect($item['permissions'])->contains(fn (string $permission): bool => auth()->user()->can($permission));
                            }
                        }
                    @endphp
                    @if ($canView)
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                class="block rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900 {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') ? 'bg-slate-100 text-slate-900' : '' }}"
                            >
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
    </aside>

    <div class="flex min-h-screen flex-1 flex-col">
        <header class="border-b border-slate-200 bg-white px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Bluebird B2B Fleet Management</p>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $header ?? 'Dashboard' }}</h2>
                </div>
                <div class="flex items-center gap-4 text-sm text-slate-600">
                    <div class="text-right">
                        <p>{{ auth()->user()?->name }}</p>
                        <p class="text-xs text-slate-500">{{ auth()->user()?->getRoleNames()->join(', ') ?: 'No Role' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium hover:bg-slate-100">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 space-y-4 p-6">
            @if (session('success'))
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
