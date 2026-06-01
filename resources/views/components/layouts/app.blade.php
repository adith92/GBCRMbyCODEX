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
                ['label' => 'Dashboard', 'roles' => ['*']],
                ['label' => 'CRM', 'roles' => ['super-admin', 'sales', 'sales-manager']],
                ['label' => 'Fleet', 'roles' => ['super-admin', 'operation', 'head-pool']],
                ['label' => 'Pool', 'roles' => ['super-admin', 'head-pool', 'pool-staff']],
                ['label' => 'Finance', 'roles' => ['super-admin', 'finance']],
                ['label' => 'HR (Backend)', 'roles' => ['super-admin']],
            ];

            $userRoles = auth()->check() ? auth()->user()->getRoleNames()->toArray() : [];
        @endphp

        <nav class="px-4 py-4">
            <ul class="space-y-1 text-sm">
                @foreach ($menuItems as $item)
                    @php
                        $visible = in_array('*', $item['roles'], true)
                            || auth()->guest()
                            || count(array_intersect($item['roles'], $userRoles)) > 0;
                    @endphp
                    @if ($visible)
                        <li>
                            <a href="#" class="block rounded-md px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900">
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
                <div class="text-sm text-slate-600">
                    {{ auth()->user()?->name ?? 'Guest Preview' }}
                </div>
            </div>
        </header>

        <main class="flex-1 p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
