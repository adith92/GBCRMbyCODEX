<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientContactRequest;
use App\Http\Requests\UpdateClientContactRequest;
use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientContactController extends Controller
{
    public function store(StoreClientContactRequest $request, Client $client): RedirectResponse
    {
        abort_unless($request->user()->can('clients.update'), 403);

        $data = $request->validated();
        $data['is_primary'] = (bool) ($data['is_primary'] ?? false);

        if ($data['is_primary']) {
            $client->contacts()->update(['is_primary' => false]);
        }

        $client->contacts()->create($data);

        return redirect()->route('crm.clients.show', $client)->with('success', 'Contact added successfully.');
    }

    public function update(UpdateClientContactRequest $request, Client $client, ClientContact $contact): RedirectResponse
    {
        abort_unless($request->user()->can('clients.update'), 403);
        abort_unless($contact->client_id === $client->id, 404);

        $data = $request->validated();
        $data['is_primary'] = (bool) ($data['is_primary'] ?? false);

        if ($data['is_primary']) {
            $client->contacts()->update(['is_primary' => false]);
        }

        $contact->update($data);

        return redirect()->route('crm.clients.show', $client)->with('success', 'Contact updated successfully.');
    }

    public function destroy(Request $request, Client $client, ClientContact $contact): RedirectResponse
    {
        abort_unless($request->user()->can('clients.update'), 403);
        abort_unless($contact->client_id === $client->id, 404);

        $contact->delete();

        return redirect()->route('crm.clients.show', $client)->with('success', 'Contact deleted successfully.');
    }
}
