<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\MaintenanceLog;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->can('reports.view'), 403);

        $outstanding = max(0, (float) Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->sum('total')
            - (float) Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->sum('paid_amount'));

        $kpis = [
            'active_clients' => Client::where('status', 'active')->count(),
            'confirmed_bookings' => Booking::whereIn('status', ['confirmed', 'completed'])->count(),
            'approved_po' => PurchaseOrder::where('status', 'approved')->count(),
            'sent_invoices' => Invoice::whereIn('status', ['sent', 'partial', 'paid', 'overdue'])->count(),
            'outstanding' => $outstanding,
            'collection' => (float) Payment::sum('amount'),
            'maintenance_open' => MaintenanceLog::whereIn('status', ['scheduled', 'in_progress'])->count(),
            'available_fleet' => Vehicle::where('status', 'available')->count(),
        ];

        $invoiceStatus = [
            'Draft' => Invoice::where('status', 'draft')->count(),
            'Sent' => Invoice::where('status', 'sent')->count(),
            'Partial' => Invoice::where('status', 'partial')->count(),
            'Paid' => Invoice::where('status', 'paid')->count(),
            'Overdue' => Invoice::where('status', 'overdue')->count(),
        ];

        $bookingStatus = [
            'Pending' => Booking::where('status', 'pending')->count(),
            'Assigned' => Booking::where('status', 'assigned')->count(),
            'Confirmed' => Booking::where('status', 'confirmed')->count(),
            'Completed' => Booking::where('status', 'completed')->count(),
            'Cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        $recentSnapshots = collect([
            ['label' => 'Client Growth', 'value' => Client::count(), 'url' => route('crm.clients.index')],
            ['label' => 'Booking Pipeline', 'value' => Booking::whereIn('status', ['pending', 'assigned', 'confirmed'])->count(), 'url' => route('bookings.index')],
            ['label' => 'Finance Open Loop', 'value' => Invoice::whereIn('status', ['sent', 'partial', 'overdue'])->count(), 'url' => route('finance.invoices.index')],
            ['label' => 'Workshop Queue', 'value' => MaintenanceLog::whereIn('status', ['scheduled', 'in_progress'])->count(), 'url' => route('maintenance.index')],
        ]);

        return view('reports.index', compact('kpis', 'invoiceStatus', 'bookingStatus', 'recentSnapshots'));
    }
}
