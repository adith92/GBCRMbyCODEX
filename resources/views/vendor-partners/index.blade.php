<x-layouts.app :title="'Partners & Vendors'" :header="'Partners & Vendors'">
    <x-breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Partners & Vendors', 'url' => route('partners.vendors.index')],
    ]" />

    <x-ui.page-header title="Partner & Vendor Network 🤝" eyebrow="Partner Ecosystem" description="Pantau workshop, rental partner, supplier, dan vendor pendukung operasional dari satu tempat.">
        <x-slot:actions>
            @can('clients.create')
                <x-ui.action-button :href="route('partners.vendors.create')" variant="primary">Tambah Partner</x-ui.action-button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Filter partner" description="Cari vendor berdasarkan nama, service type, status, atau kategori.">
        <form class="grid gap-3 md:grid-cols-4">
            <input type="text" name="q" value="{{ $search }}" class="ui-input" placeholder="Cari nama, kode, PIC">
            <select name="category" class="ui-select">
                <option value="">All Category</option>
                @foreach (['vendor' => 'Vendor', 'partner' => 'Partner', 'supplier' => 'Supplier'] as $value => $label)
                    <option value="{{ $value }}" @selected($category === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="ui-select">
                <option value="">All Status</option>
                @foreach (['active' => 'Active', 'onboarding' => 'Onboarding', 'inactive' => 'Inactive'] as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <x-ui.action-button type="submit" variant="primary">Apply</x-ui.action-button>
                <x-ui.action-button :href="route('partners.vendors.index')" variant="ghost">Reset</x-ui.action-button>
            </div>
        </form>
    </x-ui.form-card>

    <x-ui.table-card title="Vendor roster" description="Klik nama partner untuk lihat detail dan catatan operasional.">
        @if ($vendors->isEmpty())
            <div class="p-5">
                <x-ui.empty-state title="Belum ada partner/vendor" description="Tambahkan vendor workshop, rental, atau supplier untuk memperkaya demo flow." />
            </div>
        @else
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th><a class="ui-sort-link {{ $sort === 'name' ? 'is-active' : '' }}" href="{{ route('partners.vendors.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Name</a></th>
                        <th><a class="ui-sort-link {{ $sort === 'category' ? 'is-active' : '' }}" href="{{ route('partners.vendors.index', array_merge(request()->query(), ['sort' => 'category', 'direction' => $sort === 'category' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Category</a></th>
                        <th><a class="ui-sort-link {{ $sort === 'service_type' ? 'is-active' : '' }}" href="{{ route('partners.vendors.index', array_merge(request()->query(), ['sort' => 'service_type', 'direction' => $sort === 'service_type' && $direction === 'asc' ? 'desc' : 'asc'])) }}">Service</a></th>
                        <th><a class="ui-sort-link {{ $sort === 'city' ? 'is-active' : '' }}" href="{{ route('partners.vendors.index', array_merge(request()->query(), ['sort' => 'city', 'direction' => $sort === 'city' && $direction === 'asc' ? 'desc' : 'asc'])) }}">City</a></th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($vendors as $vendor)
                        <tr>
                            <td>
                                <a href="{{ route('partners.vendors.show', $vendor) }}" class="ui-link font-semibold">{{ $vendor->name }}</a>
                                <div class="mt-1 text-xs text-slate-500">{{ $vendor->code }} · {{ $vendor->contact_person ?: 'No PIC' }}</div>
                            </td>
                            <td><a class="ui-link" href="{{ route('partners.vendors.index', array_merge(request()->query(), ['category' => $vendor->category])) }}">{{ ucfirst($vendor->category) }}</a></td>
                            <td>{{ $vendor->service_type ?: '-' }}</td>
                            <td>{{ $vendor->city ?: '-' }}</td>
                            <td><a href="{{ route('partners.vendors.index', array_merge(request()->query(), ['status' => $vendor->status])) }}"><x-ui.status-badge :status="$vendor->status" /></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[#E5E7EB] px-4 py-3">{{ $vendors->links() }}</div>
        @endif
    </x-ui.table-card>
</x-layouts.app>
