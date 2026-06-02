<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Driver;
use App\Models\DriverAssignment;
use App\Models\DriverAttendance;
use App\Models\EVoucher;
use App\Models\Invoice;
use App\Models\MaintenanceLog;
use App\Models\MeetingLog;
use App\Models\Payment;
use App\Models\Pool;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->get()->keyBy('email');
        $salesUser = $users->get('sales@blueerp.test');
        $financeUser = $users->get('finance@blueerp.test');
        $operationUser = $users->get('operation@blueerp.test');
        $headPoolUser = $users->get('headpool@blueerp.test');

        $pools = Pool::factory()->count(3)->create(['status' => 'active']);
        $clients = Client::factory()->count(10)->create();

        $clients->each(function (Client $client) use ($salesUser): void {
            ClientContact::factory()->count(2)->create(['client_id' => $client->id]);
            MeetingLog::factory()->count(1)->create([
                'client_id' => $client->id,
                'user_id' => $salesUser?->id,
            ]);
        });

        MeetingLog::factory()->count(5)->create([
            'client_id' => $clients->random()->id,
            'user_id' => $salesUser?->id,
        ]);

        $vehicles = collect();
        foreach (range(1, 15) as $index) {
            $status = 'available';
            if ($index === 14) {
                $status = 'hold';
            }
            if ($index === 15) {
                $status = 'maintenance';
            }

            $vehicles->push(Vehicle::factory()->create([
                'pool_id' => $pools[($index - 1) % 3]->id,
                'status' => $status,
            ]));
        }

        $drivers = collect();
        foreach (range(1, 10) as $index) {
            $driverStatus = match ($index) {
                9 => 'sick',
                10 => 'on_leave',
                default => 'active',
            };

            $licenseExpiry = match ($index) {
                1 => now()->subDays(5)->toDateString(),
                2 => now()->addDays(10)->toDateString(),
                default => now()->addMonths(6)->toDateString(),
            };

            $drivers->push(Driver::factory()->create([
                'pool_id' => $pools[($index - 1) % 3]->id,
                'status' => $driverStatus,
                'license_expired_at' => $licenseExpiry,
            ]));
        }

        foreach (range(0, 5) as $index) {
            DriverAttendance::factory()->create([
                'driver_id' => $drivers[$index]->id,
                'attendance_date' => today()->subDays($index),
                'status' => 'present',
            ]);
        }
        DriverAttendance::factory()->create([
            'driver_id' => $drivers[8]->id,
            'attendance_date' => today(),
            'status' => 'sick',
            'notes' => 'Driver contingency scenario',
        ]);
        DriverAttendance::factory()->create([
            'driver_id' => $drivers[9]->id,
            'attendance_date' => today(),
            'status' => 'leave',
            'notes' => 'Annual leave',
        ]);

        $bookings = collect();
        $statuses = ['pending', 'assigned', 'confirmed', 'completed', 'pending', 'assigned', 'confirmed', 'completed', 'pending', 'confirmed', 'assigned', 'completed'];

        foreach ($statuses as $index => $status) {
            $vehicle = in_array($status, ['assigned', 'confirmed', 'completed'], true) ? $vehicles[$index % 10] : null;
            $driver = in_array($status, ['assigned', 'confirmed', 'completed'], true) ? $drivers[$index % 8] : null;

            $booking = Booking::factory()->create([
                'booking_number' => 'BK-'.now()->format('Ym').'-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'client_id' => $clients[$index % 10]->id,
                'requested_by' => $salesUser?->id,
                'pool_id' => $pools[$index % 3]->id,
                'vehicle_id' => $vehicle?->id,
                'driver_id' => $driver?->id,
                'status' => $status,
                'start_datetime' => now()->addDays($index + 1),
                'end_datetime' => now()->addDays($index + 1)->addHours(4),
            ]);

            if ($vehicle) {
                $vehicle->update(['status' => $status === 'assigned' ? 'po' : $vehicle->status]);
            }

            if ($driver && in_array($status, ['assigned', 'confirmed', 'completed'], true)) {
                DriverAssignment::factory()->create([
                    'booking_id' => $booking->id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'assigned_by' => $headPoolUser?->id,
                ]);
            }

            $bookings->push($booking);
        }

        $maintenanceLogs = collect();
        foreach (range(1, 6) as $index) {
            $status = match ($index) {
                1 => 'in_progress',
                2 => 'scheduled',
                3, 4 => 'completed',
                default => 'cancelled',
            };

            $vehicle = $vehicles[10 + (($index - 1) % 5)];

            $log = MaintenanceLog::factory()->create([
                'vehicle_id' => $vehicle->id,
                'reported_by' => $operationUser?->id,
                'status' => $status,
                'title' => 'Maintenance #'.$index,
            ]);

            if ($status === 'in_progress') {
                $vehicle->update(['status' => 'maintenance']);
            } elseif ($vehicle->status === 'maintenance') {
                $vehicle->update(['status' => 'available']);
            }

            $maintenanceLogs->push($log);
        }

        $purchaseOrders = collect();
        foreach (range(1, 10) as $index) {
            $booking = $bookings[$index % $bookings->count()];
            $poStatus = $index <= 8 ? ($index <= 6 ? 'invoiced' : 'approved') : 'pending';
            $subtotal = 1000000 + ($index * 100000);
            $tax = round($subtotal * 0.11, 2);

            $po = PurchaseOrder::query()->create([
                'po_number' => 'PO-'.now()->format('Ym').'-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'booking_id' => $booking->id,
                'client_id' => $booking->client_id,
                'status' => $poStatus,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
                'approved_by' => in_array($poStatus, ['approved', 'invoiced'], true) ? $financeUser?->id : null,
                'approved_at' => in_array($poStatus, ['approved', 'invoiced'], true) ? now() : null,
            ]);

            $purchaseOrders->push($po);
        }

        $invoices = collect();
        foreach (range(1, 8) as $index) {
            $po = $purchaseOrders[$index - 1];
            $status = match ($index) {
                1 => 'partial',
                2 => 'paid',
                3 => 'overdue',
                4, 5 => 'sent',
                6 => 'partial',
                default => 'draft',
            };

            $invoice = Invoice::query()->create([
                'invoice_number' => 'INV-'.now()->format('Ym').'-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'purchase_order_id' => $po->id,
                'client_id' => $po->client_id,
                'status' => $status,
                'issued_at' => now()->subDays($index)->toDateString(),
                'due_at' => $status === 'overdue' ? now()->subDays(2)->toDateString() : now()->addDays(14)->toDateString(),
                'subtotal' => $po->subtotal,
                'tax' => $po->tax,
                'total' => $po->total,
                'paid_amount' => match ($status) {
                    'partial' => round((float) $po->total / 2, 2),
                    'paid' => (float) $po->total,
                    default => 0,
                },
            ]);

            $invoices->push($invoice);
        }

        $vouchers = collect([
            EVoucher::factory()->create([
                'code' => 'EV-'.now()->format('Ym').'-0001',
                'client_id' => $clients[0]->id,
                'status' => 'active',
                'amount' => 500000,
                'used_amount' => 0,
                'expired_at' => now()->addDays(30)->toDateString(),
            ]),
            EVoucher::factory()->create([
                'code' => 'EV-'.now()->format('Ym').'-0002',
                'client_id' => $clients[1]->id,
                'status' => 'used',
                'amount' => 300000,
                'used_amount' => 300000,
                'expired_at' => now()->addDays(20)->toDateString(),
                'used_at' => now()->subDay(),
            ]),
            EVoucher::factory()->create([
                'code' => 'EV-'.now()->format('Ym').'-0003',
                'client_id' => $clients[2]->id,
                'status' => 'expired',
                'amount' => 400000,
                'used_amount' => 0,
                'expired_at' => now()->subDay()->toDateString(),
            ]),
            EVoucher::factory()->create([
                'code' => 'EV-'.now()->format('Ym').'-0004',
                'client_id' => null,
                'status' => 'active',
                'amount' => 750000,
                'used_amount' => 100000,
                'expired_at' => now()->addDays(45)->toDateString(),
            ]),
            EVoucher::factory()->create([
                'code' => 'EV-'.now()->format('Ym').'-0005',
                'client_id' => $clients[3]->id,
                'status' => 'cancelled',
                'amount' => 250000,
                'used_amount' => 0,
                'expired_at' => now()->addDays(60)->toDateString(),
            ]),
        ]);

        foreach (range(1, 8) as $index) {
            $invoice = $invoices[$index - 1];
            $amount = match ($invoice->status) {
                'partial' => round((float) $invoice->total / 2, 2),
                'paid' => (float) $invoice->total,
                default => round((float) $invoice->total / 4, 2),
            };

            $method = $index === 6 ? 'evoucher' : 'bank_transfer';
            $voucher = $method === 'evoucher' ? $vouchers[0] : null;

            if ($voucher) {
                $voucher->used_amount = (float) $voucher->used_amount + $amount;
                $voucher->save();
            }

            Payment::query()->create([
                'invoice_id' => $invoice->id,
                'e_voucher_id' => $voucher?->id,
                'payment_number' => 'PAY-'.now()->format('Ym').'-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'paid_at' => now()->subDays($index)->toDateString(),
                'amount' => $amount,
                'method' => $method,
                'reference_number' => 'REF-'.$index,
                'notes' => 'Demo payment '.$index,
                'created_by' => $financeUser?->id,
            ]);
        }
    }
}
