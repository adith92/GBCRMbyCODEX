<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Pool;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->can('vehicles.view'), 403);

        $sortBy = $request->string('sort_by')->toString() ?: 'plate_number';
        $sortDir = $request->string('sort_dir')->toString() === 'desc' ? 'desc' : 'asc';
        $allowedSorts = ['plate_number', 'brand', 'model', 'status', 'pool_name'];

        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'plate_number';
        }

        $vehicles = Vehicle::query()
            ->with('pool')
            ->when($request->string('status')->toString(), fn ($query, $status) => $query->where('status', $status))
            ->when($request->integer('pool_id'), fn ($query, $poolId) => $query->where('pool_id', $poolId))
            ->when($request->string('search')->toString(), function ($query, $search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('plate_number', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->when($sortBy === 'pool_name', fn ($query) => $query->join('pools', 'pools.id', '=', 'vehicles.pool_id')->orderBy('pools.name', $sortDir)->select('vehicles.*'))
            ->when(in_array($sortBy, ['plate_number', 'brand', 'model', 'status'], true), fn ($query) => $query->orderBy($sortBy, $sortDir))
            ->paginate(10)
            ->withQueryString();

        return view('vehicles.index', [
            'vehicles' => $vehicles,
            'pools' => Pool::query()->orderBy('name')->get(),
            'filters' => $request->only(['status', 'pool_id', 'search', 'sort_by', 'sort_dir']),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->can('vehicles.create'), 403);

        return view('vehicles.create', [
            'vehicle' => new Vehicle(),
            'pools' => Pool::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        Vehicle::create($request->validated());

        return redirect()->route('fleet.vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    public function show(Request $request, Vehicle $vehicle): View
    {
        abort_unless($request->user()->can('vehicles.view'), 403);

        $vehicle->load(['pool', 'bookings.client', 'maintenanceLogs']);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Request $request, Vehicle $vehicle): View
    {
        abort_unless($request->user()->can('vehicles.update'), 403);

        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'pools' => Pool::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return redirect()->route('fleet.vehicles.show', $vehicle)->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Request $request, Vehicle $vehicle): RedirectResponse
    {
        abort_unless($request->user()->can('vehicles.delete'), 403);

        $vehicle->delete();

        return redirect()->route('fleet.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
