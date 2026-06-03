@php($idr = fn ($amount) => 'Rp '.number_format((float) $amount, 0, ',', '.'))
<x-layouts.app :title="'Reports'" :header="'Reports Dashboard'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Reports', 'url' => route('reports.index')],
    ]" />

    <x-ui.page-header title="Reports & Insights 📈" eyebrow="Executive Reporting" description="Dashboard laporan ringkas yang lebih nyata untuk GM, finance, sales manager, dan super-admin.">
        <x-slot:actions>
            <x-ui.action-button :href="route('activity.index')" variant="ghost">Open Activity</x-ui.action-button>
        </x-slot:actions>
    </x-ui.page-header>

    <section class="ui-compact-grid md:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card label="Active Clients" :value="$kpis['active_clients']" hint="Portfolio aktif." tone="blue" :href="route('crm.clients.index', ['status' => 'active'])" link-label="CRM" />
        <x-ui.stat-card label="Confirmed Bookings" :value="$kpis['confirmed_bookings']" hint="Trip siap jalan." tone="emerald" :href="route('bookings.index', ['status' => 'confirmed'])" link-label="Bookings" />
        <x-ui.stat-card label="Approved PO" :value="$kpis['approved_po']" hint="Menunggu invoice/eksekusi." tone="amber" :href="route('finance.purchase-orders.index', ['status' => 'approved'])" link-label="PO" />
        <x-ui.stat-card label="Outstanding" :value="$idr($kpis['outstanding'])" hint="Exposure collection." tone="rose" :href="route('finance.invoices.index')" link-label="Invoices" />
        <x-ui.stat-card label="Collected" :value="$idr($kpis['collection'])" hint="Kas masuk tercatat." tone="blue" :href="route('finance.invoices.index', ['status' => 'paid'])" link-label="Payments" />
        <x-ui.stat-card label="Sent Invoices" :value="$kpis['sent_invoices']" hint="Invoice aktif." tone="slate" :href="route('finance.invoices.index')" link-label="Finance" />
        <x-ui.stat-card label="Open Maintenance" :value="$kpis['maintenance_open']" hint="Butuh monitoring." tone="amber" :href="route('maintenance.index')" link-label="Maintenance" />
        <x-ui.stat-card label="Available Fleet" :value="$kpis['available_fleet']" hint="Unit siap dispatch." tone="emerald" :href="route('fleet.vehicles.index', ['status' => 'available'])" link-label="Fleet" />
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
        <x-ui.table-card title="Report Highlights" description="Snapshot cepat untuk drill-down ke modul utama.">
            <div class="grid gap-3 p-4 md:grid-cols-2">
                @foreach ($recentSnapshots as $snapshot)
                    <a href="{{ $snapshot['url'] }}" class="ui-card-muted p-4 transition hover:border-[#D3E3F6] hover:bg-white">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#185FA5]">{{ $snapshot['label'] }}</p>
                        <p class="mt-2 text-3xl font-semibold tracking-[-0.03em] text-[#042C53]">{{ $snapshot['value'] }}</p>
                        <p class="mt-2 text-xs text-slate-500">Klik untuk drill-down ke modul terkait.</p>
                    </a>
                @endforeach
            </div>
        </x-ui.table-card>

        <x-ui.table-card title="Export-Friendly Tips" description="Belum ada export generator penuh, tapi page detail dan dashboard sudah dirapikan untuk print/export review.">
            <div class="space-y-3 p-4 text-sm text-slate-600">
                <div class="ui-card-muted p-3">🖨️ Detail page invoice, booking, PO, dan maintenance sekarang lebih siap untuk print view.</div>
                <div class="ui-card-muted p-3">📎 Gunakan browser print-to-PDF untuk paket review cepat ke stakeholder.</div>
                <div class="ui-card-muted p-3">🔎 Kombinasikan Search + Activity untuk menyusun review trail yang cepat.</div>
            </div>
        </x-ui.table-card>
    </section>

    <section class="grid gap-4 xl:grid-cols-2">
        <x-ui.table-card title="Invoice Status Mix" description="Komposisi invoice saat ini untuk review finance.">
            <div class="space-y-3 p-4">
                @foreach ($invoiceStatus as $label => $count)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600">{{ $label }}</span>
                            <span class="font-semibold text-[#042C53]">{{ $count }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-[#185FA5]" style="width: {{ max(6, min(100, $count * 8)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.table-card>

        <x-ui.table-card title="Booking Status Mix" description="Komposisi booking untuk sales, operation, dan pool.">
            <div class="space-y-3 p-4">
                @foreach ($bookingStatus as $label => $count)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-600">{{ $label }}</span>
                            <span class="font-semibold text-[#042C53]">{{ $count }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-[#378ADD]" style="width: {{ max(6, min(100, $count * 8)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.table-card>
    </section>
</x-layouts.app>
