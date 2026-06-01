<x-layouts.app :title="'Vehicles'" :header="'Fleet / Vehicles'">
    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <form method="GET" class="grid gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search plate/brand/model" class="rounded-md border-slate-300">
            <select name="status" class="rounded-md border-slate-300"><option value="">All Status</option>@foreach(['available','po','maintenance','hold'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ strtoupper($status) }}</option>@endforeach</select>
            <select name="pool_id" class="rounded-md border-slate-300"><option value="">All Pool</option>@foreach($pools as $pool)<option value="{{ $pool->id }}" @selected(($filters['pool_id'] ?? '')==$pool->id)>{{ $pool->name }}</option>@endforeach</select>
            <div class="flex gap-2"><button class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">Filter</button>@can('vehicles.create')<a href="{{ route('fleet.vehicles.create') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">+ Vehicle</a>@endcan</div>
        </form>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white">
        @if($vehicles->count() === 0)
            <p class="p-6 text-sm text-slate-500">No vehicles found.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Plate</th><th class="px-4 py-3 text-left">Pool</th><th class="px-4 py-3 text-left">Vehicle</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-right">Action</th></tr></thead>
                    <tbody>
                    @foreach($vehicles as $vehicle)
                        <tr class="border-t">
                            <td class="px-4 py-3 font-medium">{{ $vehicle->plate_number }}</td>
                            <td class="px-4 py-3">{{ $vehicle->pool?->name ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                            <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs {{ match($vehicle->status){'available'=>'bg-emerald-100 text-emerald-700','po'=>'bg-amber-100 text-amber-700','maintenance'=>'bg-rose-100 text-rose-700',default=>'bg-slate-200 text-slate-700'} }}">{{ strtoupper($vehicle->status) }}</span></td>
                            <td class="px-4 py-3 text-right">
                                <a class="text-blue-600" href="{{ route('fleet.vehicles.show', $vehicle) }}">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $vehicles->links() }}</div>
        @endif
    </section>
</x-layouts.app>
