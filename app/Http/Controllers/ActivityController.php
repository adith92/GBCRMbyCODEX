<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\MaintenanceLog;
use App\Models\MeetingLog;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if (! $this->canAccessActivity($user)) {
            abort(403);
        }

        $items = collect()
            ->concat($this->bookingItems($user))
            ->concat($this->invoiceItems($user))
            ->concat($this->paymentItems($user))
            ->concat($this->maintenanceItems($user))
            ->concat($this->meetingItems($user))
            ->sortByDesc('timestamp')
            ->take(25)
            ->values();

        return view('activity.index', [
            'items' => $items,
        ]);
    }

    private function canAccessActivity($user): bool
    {
        return $user->can('bookings.view')
            || $user->can('invoices.view')
            || $user->can('payments.view')
            || $user->can('maintenance.view')
            || $user->can('meeting-logs.view')
            || $user->can('clients.view');
    }

    private function bookingItems($user): Collection
    {
        if (! $user->can('bookings.view')) {
            return collect();
        }

        return Booking::query()->with('client')->latest()->limit(8)->get()->map(fn (Booking $booking) => [
            'type' => 'Booking',
            'title' => $booking->booking_number,
            'description' => ($booking->client?->name ?? 'Unknown client').' • '.strtoupper($booking->status),
            'timestamp' => $booking->updated_at,
            'url' => route('bookings.show', $booking),
        ]);
    }

    private function invoiceItems($user): Collection
    {
        if (! $user->can('invoices.view')) {
            return collect();
        }

        return Invoice::query()->with('client')->latest()->limit(8)->get()->map(fn (Invoice $invoice) => [
            'type' => 'Invoice',
            'title' => $invoice->invoice_number,
            'description' => ($invoice->client?->name ?? 'Unknown client').' • '.strtoupper($invoice->status),
            'timestamp' => $invoice->updated_at,
            'url' => route('finance.invoices.show', $invoice),
        ]);
    }

    private function paymentItems($user): Collection
    {
        if (! $user->can('payments.view')) {
            return collect();
        }

        return Payment::query()->with('invoice')->latest()->limit(8)->get()->map(fn (Payment $payment) => [
            'type' => 'Payment',
            'title' => $payment->payment_number,
            'description' => number_format((float) $payment->amount, 2).' • '.strtoupper($payment->method),
            'timestamp' => $payment->created_at,
            'url' => $payment->invoice ? route('finance.invoices.show', $payment->invoice) : route('finance.index'),
        ]);
    }

    private function maintenanceItems($user): Collection
    {
        if (! $user->can('maintenance.view')) {
            return collect();
        }

        return MaintenanceLog::query()->with('vehicle')->latest()->limit(8)->get()->map(fn (MaintenanceLog $log) => [
            'type' => 'Maintenance',
            'title' => $log->title,
            'description' => ($log->vehicle?->plate_number ?? 'No vehicle').' • '.strtoupper($log->status),
            'timestamp' => $log->updated_at,
            'url' => route('maintenance.show', $log),
        ]);
    }

    private function meetingItems($user): Collection
    {
        if (! $user->can('meeting-logs.view') && ! $user->can('clients.view')) {
            return collect();
        }

        return MeetingLog::query()->with('client')->latest()->limit(8)->get()->map(fn (MeetingLog $meeting) => [
            'type' => 'Meeting',
            'title' => $meeting->title,
            'description' => ($meeting->client?->name ?? 'Unknown client').' • '.strtoupper($meeting->outcome),
            'timestamp' => $meeting->updated_at,
            'url' => $meeting->client ? route('crm.clients.show', $meeting->client) : route('crm.clients.index'),
        ]);
    }
}
