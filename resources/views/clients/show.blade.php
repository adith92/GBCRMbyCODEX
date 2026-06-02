<x-layouts.app :title="'Client Detail'" :header="'Client Detail'">
    <x-breadcrumbs :items="[
        ['label' => 'CRM', 'url' => route('crm.index')],
        ['label' => 'Clients', 'url' => route('crm.clients.index')],
        ['label' => $client->name, 'url' => route('crm.clients.show', $client)],
    ]" />

    <x-ui.page-header :title="$client->name" eyebrow="Client Detail" :description="$client->legal_name ?: 'Commercial account overview with contacts, meeting logs, bookings, and finance links.'">
        <x-slot:actions>
            <x-ui.status-badge :status="$client->status" />
            <x-back-link :fallback="route('crm.clients.index')" />
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.form-card title="Client profile" description="Commercial identity and billing metadata for this account.">
        <dl class="ui-meta-grid">
            <div class="ui-meta-item"><dt>Name</dt><dd>{{ $client->name }}</dd></div>
            <div class="ui-meta-item"><dt>Legal Name</dt><dd>{{ $client->legal_name ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Tier</dt><dd><x-ui.status-badge :status="$client->tier" /></dd></div>
            <div class="ui-meta-item"><dt>Status</dt><dd><x-ui.status-badge :status="$client->status" /></dd></div>
            <div class="ui-meta-item"><dt>Industry</dt><dd>{{ $client->industry ?: '-' }}</dd></div>
            <div class="ui-meta-item"><dt>Tax Number</dt><dd>{{ $client->tax_number ?: '-' }}</dd></div>
            <div class="ui-meta-item md:col-span-2 xl:col-span-3"><dt>Billing Address</dt><dd>{{ $client->billing_address ?: '-' }}</dd></div>
        </dl>
        <div class="mt-5 flex flex-wrap gap-3">
            @can('clients.update')<x-ui.action-button :href="route('crm.clients.edit', $client)" variant="secondary">Edit</x-ui.action-button>@endcan
            @can('clients.delete')<form method="POST" action="{{ route('crm.clients.destroy', $client) }}" onsubmit="return confirm('Delete this client?')">@csrf @method('DELETE')<x-ui.action-button type="submit" variant="danger">Delete</x-ui.action-button></form>@endcan
        </div>
    </x-ui.form-card>

    <x-ui.table-card title="Contacts" description="Update primary commercial contacts without leaving the client profile.">
        <div class="space-y-3 p-5">
            @forelse($client->contacts as $contact)
                <form method="POST" action="{{ route('crm.clients.contacts.update', [$client, $contact]) }}" class="grid gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 md:grid-cols-7">
                    @csrf @method('PUT')
                    <input name="name" value="{{ $contact->name }}" class="ui-input" placeholder="Name">
                    <input name="position" value="{{ $contact->position }}" class="ui-input" placeholder="Position">
                    <input name="phone" value="{{ $contact->phone }}" class="ui-input" placeholder="Phone">
                    <input name="email" value="{{ $contact->email }}" class="ui-input" placeholder="Email">
                    <label class="mt-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><input type="checkbox" name="is_primary" value="1" @checked($contact->is_primary)>Primary</label>
                    <x-ui.action-button type="submit" variant="secondary">Update</x-ui.action-button>
                    <button form="delete-contact-{{ $contact->id }}" class="inline-flex items-center justify-center rounded-xl border border-rose-300 px-4 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50" onclick="return confirm('Delete contact?')">Delete</button>
                </form>
                <form id="delete-contact-{{ $contact->id }}" method="POST" action="{{ route('crm.clients.contacts.destroy', [$client, $contact]) }}" class="hidden">@csrf @method('DELETE')</form>
            @empty
                <x-ui.empty-state title="No contacts yet" description="Add a primary commercial contact to make follow-up easier during the demo." />
            @endforelse
        </div>
        @can('clients.update')
            <div class="border-t border-slate-200/80 p-5">
                <form method="POST" action="{{ route('crm.clients.contacts.store', $client) }}" class="grid gap-3 rounded-2xl border border-dashed border-slate-300 p-4 md:grid-cols-6">
                    @csrf
                    <input name="name" class="ui-input" placeholder="Name" required>
                    <input name="position" class="ui-input" placeholder="Position">
                    <input name="phone" class="ui-input" placeholder="Phone">
                    <input name="email" class="ui-input" placeholder="Email">
                    <label class="mt-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500"><input type="checkbox" name="is_primary" value="1">Primary</label>
                    <x-ui.action-button type="submit" variant="primary">Add Contact</x-ui.action-button>
                </form>
            </div>
        @endcan
    </x-ui.table-card>

    <x-ui.table-card title="Latest Meeting Logs" description="Commercial activity history and next-step planning.">
        <div class="space-y-3 p-5">
            @forelse($client->meetingLogs as $log)
                <div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 text-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $log->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $log->meeting_date?->format('Y-m-d') }} by {{ $log->user?->name ?? '-' }}</p>
                        </div>
                        <x-ui.status-badge :status="$log->outcome" />
                    </div>
                    <p class="mt-3 text-slate-600">{{ $log->notes }}</p>
                </div>
            @empty
                <x-ui.empty-state title="No meeting logs yet" description="Sales follow-up entries will appear here once the commercial flow starts." />
            @endforelse
        </div>
        @can('meeting-logs.create')
            <div class="border-t border-slate-200/80 p-5">
                <form method="POST" action="{{ route('crm.clients.meeting-logs.store', $client) }}" class="grid gap-3 rounded-2xl border border-dashed border-slate-300 p-4 md:grid-cols-2">
                    @csrf
                    <input type="date" name="meeting_date" class="ui-input" required>
                    <input name="title" class="ui-input" placeholder="Meeting title" required>
                    <select name="outcome" class="ui-select">@foreach(['prospecting','follow_up','negotiation','closed_won','closed_lost'] as $outcome)<option value="{{ $outcome }}">{{ strtoupper($outcome) }}</option>@endforeach</select>
                    <input type="datetime-local" name="next_follow_up_at" class="ui-input">
                    <textarea name="notes" rows="3" class="ui-textarea md:col-span-2" placeholder="Notes"></textarea>
                    <div class="md:col-span-2 flex justify-end"><x-ui.action-button type="submit" variant="primary">Add Meeting Log</x-ui.action-button></div>
                </form>
            </div>
        @endcan
    </x-ui.table-card>

    <section class="grid gap-5 xl:grid-cols-2">
        <x-ui.table-card title="Booking History" description="Recent bookings linked to this client.">
            <div class="space-y-3 p-5 text-sm">
                @forelse($client->bookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-blue-200 hover:bg-blue-50/50">
                        <div class="flex items-start justify-between gap-3"><p class="font-semibold text-slate-900">{{ $booking->booking_number }}</p><x-ui.status-badge :status="$booking->status" /></div>
                    </a>
                @empty
                    <x-ui.empty-state title="No booking history yet" description="Bookings created for this client will show up here automatically." />
                @endforelse
            </div>
        </x-ui.table-card>

        <x-ui.table-card title="Invoices" description="Finance drill-down connected to this client.">
            <div class="space-y-3 p-5 text-sm">
                @forelse($client->invoices as $invoice)
                    <a href="{{ route('finance.invoices.show', $invoice) }}" class="block rounded-2xl border border-slate-200/80 bg-slate-50/70 px-4 py-3 transition hover:border-emerald-200 hover:bg-emerald-50/40">
                        <div class="flex items-start justify-between gap-3"><p class="font-semibold text-slate-900">{{ $invoice->invoice_number }}</p><x-ui.status-badge :status="$invoice->status" /></div>
                    </a>
                @empty
                    <x-ui.empty-state title="No invoices yet" description="Invoices generated through finance flow will appear here." />
                @endforelse
            </div>
        </x-ui.table-card>
    </section>
</x-layouts.app>
