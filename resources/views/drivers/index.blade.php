<x-layouts.app :title="'Drivers'" :header="'Drivers'">
    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <form method="GET" class="grid gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name/phone/employee" class="rounded-md border-slate-300">
            <select name="status" class="rounded-md border-slate-300"><option value="">All Status</option>@foreach(['active','inactive','sick','on_leave'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ strtoupper($status) }}</option>@endforeach</select>
            <select name="pool_id" class="rounded-md border-slate-300"><option value="">All Pool</option>@foreach($pools as $pool)<option value="{{ $pool->id }}" @selected(($filters['pool_id'] ?? '')==$pool->id)>{{ $pool->name }}</option>@endforeach</select>
            <div class="flex gap-2"><button class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">Filter</button>@can('drivers.create')<a href="{{ route('drivers.create') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">+ Driver</a>@endcan</div>
        </form>
    </section>
    <section class="rounded-lg border border-slate-200 bg-white">
        @if($drivers->count()===0)
            <p class="p-6 text-sm text-slate-500">No drivers found.</p>
        @else
            <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Pool</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">License</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody>
                @foreach($drivers as $driver)
                @php $isExpired = $driver->license_expired_at && $driver->license_expired_at->isPast(); @endphp
                <tr class="border-t"><td class="px-4 py-3">{{ $driver->name }}<div class="text-xs text-slate-500">{{ $driver->employee_code ?: '-' }}</div></td><td class="px-4 py-3">{{ $driver->pool?->name ?? '-' }}</td><td class="px-4 py-3">{{ strtoupper($driver->status) }}</td><td class="px-4 py-3">@if($driver->license_expired_at)<span class="text-xs {{ $isExpired ? 'text-red-600' : 'text-slate-600' }}">{{ $driver->license_expired_at->format('Y-m-d') }} {{ $isExpired ? '(EXPIRED)' : '' }}</span>@else-@endif</td><td class="px-4 py-3 text-right"><a class="text-blue-600" href="{{ route('drivers.show', $driver) }}">Detail</a></td></tr>
                @endforeach
            </tbody></table></div>
            <div class="p-4">{{ $drivers->links() }}</div>
        @endif
    </section>
</x-layouts.app>
