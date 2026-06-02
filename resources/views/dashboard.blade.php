@php
    $user = auth()->user();
    $activeClients = \App\Models\Client::query()->where('status', 'active')->count();
    $activeBookings = \App\Models\Booking::query()->whereIn('status', ['pending', 'assigned', 'confirmed'])->count();
    $availableVehicles = \App\Models\Vehicle::query()->where('status', 'available')->count();
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

    <section class="grid gap-3 md:grid-cols-4">
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Total Active Clients</p><p class="mt-1 text-2xl font-semibold">{{ $activeClients }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Active Bookings</p><p class="mt-1 text-2xl font-semibold">{{ $activeBookings }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Available Vehicles</p><p class="mt-1 text-2xl font-semibold">{{ $availableVehicles }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Vehicles in Maintenance</p><p class="mt-1 text-2xl font-semibold">{{ $vehiclesInMaintenance }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Outstanding Invoice Amount</p><p class="mt-1 text-2xl font-semibold">{{ number_format(max(0, $outstandingInvoiceAmount), 2) }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Overdue Invoices</p><p class="mt-1 text-2xl font-semibold">{{ $overdueInvoices }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Today Pool Queue Count</p><p class="mt-1 text-2xl font-semibold">{{ $todayPoolQueueCount }}</p></div>
        <div class="rounded-lg border bg-white p-4"><p class="text-xs text-slate-500">Current Role</p><p class="mt-1 text-lg font-semibold">{{ $user?->getRoleNames()->join(', ') }}</p></div>
    </section>

    <section class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-lg border bg-white p-4">
            <h3 class="text-sm font-semibold">Latest Bookings</h3>
            <div class="mt-3 space-y-2 text-sm">
                @foreach ($latestBookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}" class="block rounded border p-2 hover:bg-slate-50">
                        {{ $booking->booking_number }} - {{ $booking->client?->name ?? '-' }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="rounded-lg border bg-white p-4">
            <h3 class="text-sm font-semibold">Latest Payments</h3>
            <div class="mt-3 space-y-2 text-sm">
                @foreach ($latestPayments as $payment)
                    <a href="{{ route('finance.invoices.show', $payment->invoice) }}" class="block rounded border p-2 hover:bg-slate-50">
                        {{ $payment->payment_number }} - {{ number_format($payment->amount, 2) }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="rounded-lg border bg-white p-4">
            <h3 class="text-sm font-semibold">Latest Maintenance Logs</h3>
            <div class="mt-3 space-y-2 text-sm">
                @foreach ($latestMaintenanceLogs as $log)
                    <a href="{{ route('maintenance.show', $log) }}" class="block rounded border p-2 hover:bg-slate-50">
                        {{ $log->title }} - {{ $log->vehicle?->plate_number ?? '-' }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
