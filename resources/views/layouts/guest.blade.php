<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GBCRMbyCODEX') }}</title>

        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-bb-page antialiased">
        <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(55,138,221,0.12),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(4,44,83,0.1),_transparent_28%)]"></div>

            <div class="relative grid w-full max-w-6xl overflow-hidden rounded-[20px] border border-[#E5E7EB] bg-white shadow-[0_28px_70px_rgba(4,44,83,0.12)] lg:grid-cols-[1.05fr_0.95fr]">
                <div class="hidden bg-[#042C53] p-10 text-white lg:flex lg:flex-col lg:justify-between">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-[#9FCCF5]">GBCRMbyCODEX</p>
                        <h1 class="mt-4 text-[34px] font-semibold leading-tight tracking-[-0.04em]">Golden Bird Corporate CRM</h1>
                        <p class="mt-4 max-w-md text-sm leading-7 text-slate-200/90">
                            Platform operasional terpadu untuk client, fleet, dispatch, finance, maintenance, dan workflow admin terbatas.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-[14px] border border-white/10 bg-white/8 p-4 backdrop-blur-sm">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#9FCCF5]">Business Modules</p>
                            <div class="mt-3 grid grid-cols-2 gap-2 text-sm text-white/90">
                                <span>CRM Clients</span>
                                <span>Fleet & Drivers</span>
                                <span>Bookings</span>
                                <span>Pool Dispatch</span>
                                <span>Finance Flow</span>
                                <span>Maintenance</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-slate-200/90">
                            <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1">Laravel 12</span>
                            <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1">Livewire 3</span>
                            <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1">Spatie RBAC</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center p-6 sm:p-10">
                    <div class="w-full max-w-md">
                        <div class="mb-8 lg:hidden">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#185FA5]">GBCRMbyCODEX</p>
                            <h1 class="mt-3 text-[28px] font-semibold tracking-[-0.04em] text-[#042C53]">Golden Bird Corporate CRM</h1>
                        </div>

                        <div class="mb-8 flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-[14px] bg-[#EEF4FA] text-[#185FA5] shadow-sm">
                                <x-application-logo class="h-8 w-8 fill-current" />
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#185FA5]">Secure Access</p>
                                <h2 class="mt-1 text-[28px] font-semibold tracking-[-0.04em] text-[#042C53]">Welcome Back</h2>
                                <p class="mt-1 text-sm text-slate-500">Masuk ke workspace operasional untuk melanjutkan aktivitas harian.</p>
                            </div>
                        </div>

                        <div class="rounded-[16px] border border-[#E5E7EB] bg-white p-6 shadow-[0_16px_40px_rgba(4,44,83,0.06)] sm:p-7">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
