<x-layouts.app :title="'Client Detail'" :header="'Client Detail'">
    <section class="rounded-lg border border-slate-200 bg-white p-4 text-sm">
        <div class="grid gap-2 md:grid-cols-2">
            <p><strong>Name:</strong> {{ $client->name }}</p><p><strong>Legal Name:</strong> {{ $client->legal_name ?: '-' }}</p>
            <p><strong>Tier:</strong> {{ strtoupper($client->tier) }}</p><p><strong>Status:</strong> {{ strtoupper($client->status) }}</p>
            <p><strong>Industry:</strong> {{ $client->industry ?: '-' }}</p><p><strong>Tax Number:</strong> {{ $client->tax_number ?: '-' }}</p>
        </div>
        <p class="mt-3"><strong>Address:</strong> {{ $client->billing_address ?: '-' }}</p>
        <div class="mt-4 flex gap-2">
            @can('clients.update')<a href="{{ route('crm.clients.edit', $client) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Edit</a>@endcan
            @can('clients.delete')<form method="POST" action="{{ route('crm.clients.destroy', $client) }}" onsubmit="return confirm('Delete this client?')">@csrf @method('DELETE')<button class="rounded-md border border-red-300 px-3 py-2 text-sm text-red-600">Delete</button></form>@endcan
            <a href="{{ route('crm.clients.index') }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm">Back</a>
        </div>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <h3 class="text-base font-semibold">Contacts</h3>
        @if($client->contacts->isEmpty())<p class="mt-2 text-sm text-slate-500">No contacts yet.</p>@endif
        <div class="mt-3 space-y-2">
            @foreach($client->contacts as $contact)
            <form method="POST" action="{{ route('crm.clients.contacts.update', [$client, $contact]) }}" class="grid gap-2 rounded-md border p-3 md:grid-cols-7">
                @csrf @method('PUT')
                <input name="name" value="{{ $contact->name }}" class="rounded-md border-slate-300 text-sm" placeholder="Name">
                <input name="position" value="{{ $contact->position }}" class="rounded-md border-slate-300 text-sm" placeholder="Position">
                <input name="phone" value="{{ $contact->phone }}" class="rounded-md border-slate-300 text-sm" placeholder="Phone">
                <input name="email" value="{{ $contact->email }}" class="rounded-md border-slate-300 text-sm" placeholder="Email">
                <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="is_primary" value="1" @checked($contact->is_primary)>Primary</label>
                <button class="rounded-md border border-slate-300 px-2 py-1 text-xs">Update</button>
                <button form="delete-contact-{{ $contact->id }}" class="rounded-md border border-red-300 px-2 py-1 text-xs text-red-600" onclick="return confirm('Delete contact?')">Delete</button>
            </form>
            <form id="delete-contact-{{ $contact->id }}" method="POST" action="{{ route('crm.clients.contacts.destroy', [$client, $contact]) }}" class="hidden">@csrf @method('DELETE')</form>
            @endforeach
        </div>

        @can('clients.update')
        <form method="POST" action="{{ route('crm.clients.contacts.store', $client) }}" class="mt-4 grid gap-2 rounded-md border border-dashed p-3 md:grid-cols-6">
            @csrf
            <input name="name" class="rounded-md border-slate-300 text-sm" placeholder="Name" required>
            <input name="position" class="rounded-md border-slate-300 text-sm" placeholder="Position">
            <input name="phone" class="rounded-md border-slate-300 text-sm" placeholder="Phone">
            <input name="email" class="rounded-md border-slate-300 text-sm" placeholder="Email">
            <label class="flex items-center gap-2 text-xs"><input type="checkbox" name="is_primary" value="1">Primary</label>
            <button class="rounded-md bg-slate-900 px-2 py-1 text-xs text-white">Add Contact</button>
        </form>
        @endcan
    </section>

    <section class="rounded-lg border border-slate-200 bg-white p-4">
        <h3 class="text-base font-semibold">Latest Meeting Logs</h3>
        @if($client->meetingLogs->isEmpty())<p class="mt-2 text-sm text-slate-500">No meeting logs yet.</p>@endif
        <ul class="mt-3 space-y-2">
            @foreach($client->meetingLogs as $log)
                <li class="rounded-md border p-3 text-sm"><p class="font-medium">{{ $log->title }} ({{ strtoupper($log->outcome) }})</p><p class="text-xs text-slate-500">{{ $log->meeting_date?->format('Y-m-d') }} by {{ $log->user?->name ?? '-' }}</p><p class="mt-1">{{ $log->notes }}</p></li>
            @endforeach
        </ul>

        @can('meeting-logs.create')
        <form method="POST" action="{{ route('crm.clients.meeting-logs.store', $client) }}" class="mt-4 grid gap-2 rounded-md border border-dashed p-3 md:grid-cols-2">
            @csrf
            <input type="date" name="meeting_date" class="rounded-md border-slate-300 text-sm" required>
            <input name="title" class="rounded-md border-slate-300 text-sm" placeholder="Meeting title" required>
            <select name="outcome" class="rounded-md border-slate-300 text-sm">@foreach(['prospecting','follow_up','negotiation','closed_won','closed_lost'] as $outcome)<option value="{{ $outcome }}">{{ strtoupper($outcome) }}</option>@endforeach</select>
            <input type="datetime-local" name="next_follow_up_at" class="rounded-md border-slate-300 text-sm">
            <textarea name="notes" rows="3" class="rounded-md border-slate-300 text-sm md:col-span-2" placeholder="Notes"></textarea>
            <button class="rounded-md bg-slate-900 px-3 py-2 text-sm text-white md:col-span-2">Add Meeting Log</button>
        </form>
        @endcan
    </section>
</x-layouts.app>
