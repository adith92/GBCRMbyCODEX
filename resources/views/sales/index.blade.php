@php
    $idr = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.');
@endphp

<x-layouts.app :title="'Sales'" :header="'Sales'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Sales', 'url' => route('sales.index')],
    ]" />

    <x-ui.page-header title="Sales performance overview" eyebrow="Sales" description="Ringkasan performa tim sales yang bisa dipakai GM, super-admin, dan sales untuk review cepat pipeline komersial." />

    <x-ui.table-card title="Sales roster" description="Klik nama sales untuk membuka performance detail dan riwayat aktivitas komersial.">
        @if ($salesUsers->isEmpty())
            <div class="p-5">
                <x-ui.empty-state title="No sales users found" description="Tambahkan user dengan role sales atau sales-manager untuk mulai memantau performa komersial." />
            </div>
        @else
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>Sales</th>
                        <th>Clients</th>
                        <th>Bookings</th>
                        <th>Revenue</th>
                        <th>Paid</th>
                        <th>Last Activity</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($salesUsers as $row)
                        <tr>
                            <td>
                                <a href="{{ route('sales.performance', $row['user']) }}" class="ui-link font-semibold text-slate-900">{{ $row['user']->name }}</a>
                                <p class="mt-1 text-xs text-slate-500">{{ $row['user']->email }}</p>
                            </td>
                            <td>{{ $row['total_clients'] }}</td>
                            <td>{{ $row['total_bookings'] }}</td>
                            <td>{{ $idr($row['total_revenue']) }}</td>
                            <td>{{ $idr($row['total_paid']) }}</td>
                            <td>{{ $row['last_activity'] ? \Illuminate\Support\Carbon::parse($row['last_activity'])->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
