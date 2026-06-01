<x-layouts.app :title="'Clients'" :header="'CRM / Clients'">
    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <form method="GET" class="grid gap-3 md:grid-cols-5">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name/legal name" class="rounded-md border-slate-300">
            <select name="tier" class="rounded-md border-slate-300"><option value="">All Tier</option>@foreach(['bronze','silver','gold','platinum'] as $tier)<option value="{{ $tier }}" @selected(($filters['tier'] ?? '')===$tier)>{{ strtoupper($tier) }}</option>@endforeach</select>
            <select name="status" class="rounded-md border-slate-300"><option value="">All Status</option>@foreach(['active','inactive','prospect'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '')===$status)>{{ strtoupper($status) }}</option>@endforeach</select>
            <button class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white">Filter</button>
            <div>@can('clients.create')<a href="{{ route('crm.clients.create') }}" class="inline-block rounded-md border border-slate-300 px-3 py-2 text-sm">+ Client</a>@endcan</div>
        </form>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white">
        @if($clients->count()===0)
            <p class="p-6 text-sm text-slate-500">No clients found.</p>
        @else
            <div class="overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-slate-50"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Tier</th><th class="px-4 py-3 text-left">Status</th><th class="px-4 py-3 text-left">Contacts</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody>
                @foreach($clients as $client)
                    <tr class="border-t"><td class="px-4 py-3 font-medium">{{ $client->name }}<div class="text-xs text-slate-500">{{ $client->legal_name ?: '-' }}</div></td><td class="px-4 py-3">{{ strtoupper($client->tier) }}</td><td class="px-4 py-3">{{ strtoupper($client->status) }}</td><td class="px-4 py-3">{{ $client->contacts_count }}</td><td class="px-4 py-3 text-right"><a class="text-blue-600" href="{{ route('crm.clients.show', $client) }}">Detail</a></td></tr>
                @endforeach
            </tbody></table></div>
            <div class="p-4">{{ $clients->links() }}</div>
        @endif
    </section>
</x-layouts.app>
