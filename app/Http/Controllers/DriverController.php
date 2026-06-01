<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use App\Models\Pool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('drivers.view'), 403);

        $drivers = Driver::query()
            ->with('pool')
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->integer('pool_id'), fn ($query, $poolId) => $query->where('pool_id', $poolId))
            ->when($request->string('search')->toString(), function ($query, $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('drivers.index', [
            'drivers' => $drivers,
            'pools' => Pool::query()->orderBy('name')->get(),
            'filters' => $request->only(['status', 'pool_id', 'search']),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('drivers.create'), 403);

        return view('drivers.create', [
            'driver' => new Driver(),
            'pools' => Pool::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreDriverRequest $request): RedirectResponse
    {
        Driver::create($request->validated());

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    public function show(Request $request, Driver $driver): View
    {
        abort_unless($request->user()->can('drivers.view'), 403);

        $driver->load(['pool', 'bookings.client', 'assignments.booking']);

        return view('drivers.show', compact('driver'));
    }

    public function edit(Request $request, Driver $driver): View
    {
        abort_unless($request->user()->can('drivers.update'), 403);

        return view('drivers.edit', [
            'driver' => $driver,
            'pools' => Pool::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        $driver->update($request->validated());

        return redirect()->route('drivers.show', $driver)->with('success', 'Driver updated successfully.');
    }

    public function destroy(Request $request, Driver $driver): RedirectResponse
    {
        abort_unless($request->user()->can('drivers.delete'), 403);

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
    }
}
