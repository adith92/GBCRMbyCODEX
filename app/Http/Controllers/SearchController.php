<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Invoice;
use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim($request->string('q')->toString());
        $user = $request->user();

        if (! $this->canAccessSearch($user)) {
            abort(403);
        }

        $results = collect();

        if ($query !== '') {
            $results = $results
                ->concat($this->searchClients($user, $query))
                ->concat($this->searchVehicles($user, $query))
                ->concat($this->searchDrivers($user, $query))
                ->concat($this->searchBookings($user, $query))
                ->concat($this->searchInvoices($user, $query))
                ->concat($this->searchMaintenance($user, $query))
                ->sortBy('label')
                ->values();
        }

        return view('search.index', [
            'query' => $query,
            'results' => $results,
        ]);
    }

    private function canAccessSearch($user): bool
    {
        return $user->can('clients.view')
            || $user->can('vehicles.view')
            || $user->can('drivers.view')
            || $user->can('bookings.view')
            || $user->can('invoices.view')
            || $user->can('maintenance.view')
            || $user->can('meeting-logs.view');
    }

    private function searchClients($user, string $query): Collection
    {
        if (! $user->can('clients.view')) {
            return collect();
        }

        return Client::query()
            ->where(function ($builder) use ($query): void {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('legal_name', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(fn (Client $client) => [
                'type' => 'Client',
                'label' => $client->name,
                'meta' => $client->legal_name ?: strtoupper($client->status),
                'url' => route('crm.clients.show', $client),
            ]);
    }

    private function searchVehicles($user, string $query): Collection
    {
        if (! $user->can('vehicles.view')) {
            return collect();
        }

        return Vehicle::query()
            ->where(function ($builder) use ($query): void {
                $builder->where('plate_number', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%")
                    ->orWhere('model', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(fn (Vehicle $vehicle) => [
                'type' => 'Vehicle',
                'label' => $vehicle->plate_number,
                'meta' => trim(($vehicle->brand ?? '').' '.($vehicle->model ?? '')) ?: strtoupper($vehicle->status),
                'url' => route('fleet.vehicles.show', $vehicle),
            ]);
    }

    private function searchDrivers($user, string $query): Collection
    {
        if (! $user->can('drivers.view')) {
            return collect();
        }

        return Driver::query()
            ->where(function ($builder) use ($query): void {
                $builder->where('name', 'like', "%{$query}%")
                    ->orWhere('employee_code', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(fn (Driver $driver) => [
                'type' => 'Driver',
                'label' => $driver->name,
                'meta' => $driver->employee_code ?: strtoupper($driver->status),
                'url' => route('drivers.show', $driver),
            ]);
    }

    private function searchBookings($user, string $query): Collection
    {
        if (! $user->can('bookings.view')) {
            return collect();
        }

        return Booking::query()
            ->with('client')
            ->where(function ($builder) use ($query): void {
                $builder->where('booking_number', 'like', "%{$query}%")
                    ->orWhereHas('client', fn ($client) => $client->where('name', 'like', "%{$query}%"));
            })
            ->limit(5)
            ->get()
            ->map(fn (Booking $booking) => [
                'type' => 'Booking',
                'label' => $booking->booking_number,
                'meta' => $booking->client?->name ?? strtoupper($booking->status),
                'url' => route('bookings.show', $booking),
            ]);
    }

    private function searchInvoices($user, string $query): Collection
    {
        if (! $user->can('invoices.view')) {
            return collect();
        }

        return Invoice::query()
            ->with('client')
            ->where('invoice_number', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(fn (Invoice $invoice) => [
                'type' => 'Invoice',
                'label' => $invoice->invoice_number,
                'meta' => $invoice->client?->name ?? strtoupper($invoice->status),
                'url' => route('finance.invoices.show', $invoice),
            ]);
    }

    private function searchMaintenance($user, string $query): Collection
    {
        if (! $user->can('maintenance.view')) {
            return collect();
        }

        return MaintenanceLog::query()
            ->with('vehicle')
            ->where(function ($builder) use ($query): void {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhereHas('vehicle', fn ($vehicle) => $vehicle->where('plate_number', 'like', "%{$query}%"));
            })
            ->limit(5)
            ->get()
            ->map(fn (MaintenanceLog $log) => [
                'type' => 'Maintenance',
                'label' => $log->title,
                'meta' => $log->vehicle?->plate_number ?? strtoupper($log->status),
                'url' => route('maintenance.show', $log),
            ]);
    }
}
