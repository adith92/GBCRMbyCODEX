<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\MeetingLog;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($this->canViewSalesArea($request->user()), 403);

        $salesUsers = User::query()
            ->role(['sales', 'sales-manager'])
            ->withCount([
                'meetingLogs',
                'requestedBookings as total_bookings',
            ])
            ->orderBy('name')
            ->get()
            ->map(function (User $user): array {
                $clientIds = MeetingLog::query()
                    ->where('user_id', $user->id)
                    ->distinct()
                    ->pluck('client_id');

                $bookingIds = Booking::query()
                    ->where('requested_by', $user->id)
                    ->pluck('id');

                $revenue = Invoice::query()
                    ->whereIn('purchase_order_id', function ($query) use ($bookingIds): void {
                        $query->select('id')
                            ->from('purchase_orders')
                            ->whereIn('booking_id', $bookingIds);
                    })
                    ->sum('total');

                $paid = Payment::query()
                    ->whereIn('invoice_id', function ($query) use ($bookingIds): void {
                        $query->select('invoices.id')
                            ->from('invoices')
                            ->join('purchase_orders', 'purchase_orders.id', '=', 'invoices.purchase_order_id')
                            ->whereIn('purchase_orders.booking_id', $bookingIds);
                    })
                    ->sum('amount');

                $lastActivity = collect([
                    $user->meetingLogs()->latest('meeting_date')->value('meeting_date'),
                    $user->requestedBookings()->latest('updated_at')->value('updated_at'),
                ])->filter()->max();

                return [
                    'user' => $user,
                    'total_clients' => Client::query()->whereIn('id', $clientIds)->count(),
                    'total_bookings' => $user->total_bookings,
                    'total_revenue' => (float) $revenue,
                    'total_paid' => (float) $paid,
                    'last_activity' => $lastActivity,
                ];
            });

        return view('sales.index', compact('salesUsers'));
    }

    public function performance(Request $request, User $user): View
    {
        abort_unless($this->canViewSalesUser($request->user(), $user), 403);

        $meetingClientIds = MeetingLog::query()
            ->where('user_id', $user->id)
            ->distinct()
            ->pluck('client_id');

        $bookings = Booking::query()
            ->with(['client', 'pool'])
            ->where('requested_by', $user->id)
            ->latest('start_datetime')
            ->limit(10)
            ->get();

        $purchaseOrderIds = DB::table('purchase_orders')
            ->whereIn('booking_id', Booking::query()->where('requested_by', $user->id)->select('id'))
            ->pluck('id');

        $invoices = Invoice::query()
            ->with('client')
            ->whereIn('purchase_order_id', $purchaseOrderIds)
            ->latest('issued_at')
            ->limit(10)
            ->get();

        $payments = Payment::query()
            ->whereIn('invoice_id', $invoices->pluck('id'))
            ->sum('amount');

        $summary = [
            'total_clients' => Client::query()->whereIn('id', $meetingClientIds)->count(),
            'total_bookings' => Booking::query()->where('requested_by', $user->id)->count(),
            'confirmed_bookings' => Booking::query()->where('requested_by', $user->id)->whereIn('status', ['confirmed', 'completed'])->count(),
            'total_revenue' => (float) Invoice::query()->whereIn('purchase_order_id', $purchaseOrderIds)->sum('total'),
            'total_paid' => (float) $payments,
            'last_activity' => collect([
                $user->meetingLogs()->latest('meeting_date')->value('meeting_date'),
                Booking::query()->where('requested_by', $user->id)->latest('updated_at')->value('updated_at'),
            ])->filter()->max(),
        ];

        return view('sales.performance', compact('user', 'summary', 'bookings', 'invoices'));
    }

    private function canViewSalesArea(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['super-admin', 'gm', 'sales-manager', 'sales']) && $user->can('dashboard.view');
    }

    private function canViewSalesUser(?User $viewer, User $subject): bool
    {
        if (! $viewer || ! $viewer->can('dashboard.view')) {
            return false;
        }

        if ($viewer->hasAnyRole(['super-admin', 'gm', 'sales-manager'])) {
            return true;
        }

        return $viewer->id === $subject->id && $viewer->hasRole('sales');
    }
}
