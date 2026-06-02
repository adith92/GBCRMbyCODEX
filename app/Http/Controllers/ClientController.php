<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('clients.view'), 403);

        $clients = Client::query()
            ->withCount('contacts')
            ->when($request->string('tier')->toString(), fn ($query, $tier) => $query->where('tier', $tier))
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->string('search')->toString(), function ($query, $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('legal_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', [
            'clients' => $clients,
            'filters' => $request->only(['tier', 'status', 'search']),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('clients.create'), 403);

        return view('clients.create', [
            'client' => new Client(),
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = Client::create($request->validated());

        return redirect()->route('crm.clients.show', $client)->with('success', 'Client created successfully.');
    }

    public function show(Request $request, Client $client): View
    {
        abort_unless($request->user()->can('clients.view'), 403);

        $client->load([
            'contacts',
            'meetingLogs' => fn ($query) => $query->latest()->limit(10),
            'bookings' => fn ($query) => $query->latest()->limit(10),
            'invoices' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('clients.show', compact('client'));
    }

    public function edit(Request $request, Client $client): View
    {
        abort_unless($request->user()->can('clients.update'), 403);

        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()->route('crm.clients.show', $client)->with('success', 'Client updated successfully.');
    }

    public function destroy(Request $request, Client $client): RedirectResponse
    {
        abort_unless($request->user()->can('clients.delete'), 403);

        $client->delete();

        return redirect()->route('crm.clients.index')->with('success', 'Client deleted successfully.');
    }
}
