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
use App\Models\VendorPartner;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! $this->demoSeedEnabled()) {
            return;
        }

        $users = User::query()->get()->keyBy('email');

        if ($this->seedMode() === 'stress') {
            $this->seedStressData($users);

            return;
        }

        $this->seedCuratedDemoData($users);
    }

    protected function seedCuratedDemoData(Collection $users): void
    {
        $salesUser = $users->get('sales@blueerp.test');
        $financeUser = $users->get('finance@blueerp.test');
        $operationUser = $users->get('operation@blueerp.test');
        $headPoolUser = $users->get('headpool@blueerp.test');

        $pools = Pool::factory()->count(3)->create(['status' => 'active']);
        $clientCount = $this->demoCustomerCount();
        $clients = Client::factory()->count($clientCount)->create();

        $featuredClients = $clients->take(min(10, $clients->count()));
        $this->seedVendorPartners();
        $featuredClients->each(function (Client $client) use ($salesUser): void {
            ClientContact::factory()->count(2)->create(['client_id' => $client->id]);
            MeetingLog::factory()->count(1)->create([
                'client_id' => $client->id,
                'user_id' => $salesUser?->id,
            ]);
        });

        $supportingClients = $clients->slice($featuredClients->count())->take(20)->values();
        $supportingClients->each(function (Client $client, int $index) use ($salesUser): void {
            ClientContact::factory()->create([
                'client_id' => $client->id,
                'is_primary' => true,
            ]);

            if ($index % 2 === 0) {
                MeetingLog::factory()->create([
                    'client_id' => $client->id,
                    'user_id' => $salesUser?->id,
                ]);
            }
        });

        MeetingLog::factory()->count(max(5, min(15, (int) floor($clientCount / 3))))->create([
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
                'client_id' => $clients[$index % $clients->count()]->id,
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

        $this->seedMaintenanceLogs($vehicles, $operationUser?->id);
        $purchaseOrders = $this->seedPurchaseOrders($bookings, $financeUser?->id, 10, 8);
        $invoices = $this->seedInvoices($purchaseOrders, 8);
        $vouchers = $this->seedEVouchers($clients);
        $this->seedPayments($invoices, $vouchers, $financeUser?->id, 8);
    }

    protected function seedVendorPartners(): void
    {
        collect([
            ['name' => 'Bluebird Workshop Central', 'category' => 'vendor', 'service_type' => 'Workshop', 'city' => 'Jakarta', 'status' => 'active'],
            ['name' => 'Skyline Rental Support', 'category' => 'partner', 'service_type' => 'Rental Backup Unit', 'city' => 'Bekasi', 'status' => 'active'],
            ['name' => 'PT Sinar Ban Nusantara', 'category' => 'supplier', 'service_type' => 'Tyre Supplier', 'city' => 'Tangerang', 'status' => 'active'],
            ['name' => 'Mitra Driver Outsource', 'category' => 'partner', 'service_type' => 'Driver Outsource', 'city' => 'Depok', 'status' => 'onboarding'],
            ['name' => 'Auto Parts Prime', 'category' => 'supplier', 'service_type' => 'Sparepart Supplier', 'city' => 'Jakarta', 'status' => 'active'],
        ])->each(function (array $row, int $index): void {
            VendorPartner::query()->create([
                'code' => 'VP-'.now()->format('Ym').'-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'name' => $row['name'],
                'category' => $row['category'],
                'service_type' => $row['service_type'],
                'contact_person' => 'PIC '.($index + 1),
                'phone' => '+62-811-77'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'email' => 'partner'.($index + 1).'@blueerp.test',
                'city' => $row['city'],
                'status' => $row['status'],
                'notes' => 'Seeded partner/vendor for procurement, maintenance, and backup dispatch scenarios.',
            ]);
        });
    }

    protected function seedStressData(Collection $users): void
    {
        $salesUser = $users->get('sales@blueerp.test');
        $financeUser = $users->get('finance@blueerp.test');
        $operationUser = $users->get('operation@blueerp.test');
        $headPoolUser = $users->get('headpool@blueerp.test');

        $pools = Pool::factory()->count(3)->create(['status' => 'active']);
        $clientCount = $this->stressCustomerCount();
        $timestamp = now();
        $clientRows = [];
        $tiers = ['bronze', 'silver', 'gold', 'platinum'];
        $industries = ['logistics', 'technology', 'manufacturing', 'retail', 'services'];
        $statuses = ['active', 'active', 'active', 'prospect', 'inactive'];

        foreach (range(1, $clientCount) as $index) {
            $label = str_pad((string) $index, 6, '0', STR_PAD_LEFT);
            $clientRows[] = [
                'code' => 'CL-STRESS-'.$label,
                'name' => 'Stress Client '.$label,
                'legal_name' => 'Stress Client '.$label.' LLC',
                'tier' => $tiers[$index % count($tiers)],
                'industry' => $industries[$index % count($industries)],
                'tax_number' => 'NPWP-'.str_pad((string) $index, 10, '0', STR_PAD_LEFT),
                'billing_address' => 'Stress Street '.$index.', Jakarta',
                'status' => $statuses[$index % count($statuses)],
                'notes' => $index % 12 === 0 ? 'High volume seeded account' : null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            if (count($clientRows) === 300) {
                DB::table('clients')->insert($clientRows);
                $clientRows = [];
            }
        }

        if ($clientRows !== []) {
            DB::table('clients')->insert($clientRows);
        }

        $clients = Client::query()->orderBy('id')->get(['id']);
        $clientIds = $clients->pluck('id')->all();

        $contactCount = min(250, max(150, (int) floor($clientCount * 0.17)));
        $contactRows = [];
        foreach (range(1, $contactCount) as $index) {
            $clientId = $clientIds[$index - 1];
            $contactRows[] = [
                'client_id' => $clientId,
                'name' => 'PIC Stress '.$index,
                'position' => $index % 3 === 0 ? 'Procurement' : 'Operations',
                'phone' => '+62-811-'.str_pad((string) (700000 + $index), 6, '0', STR_PAD_LEFT),
                'email' => 'stress-pic-'.$index.'@blueerp.test',
                'is_primary' => true,
                'notes' => $index % 10 === 0 ? 'Bulk seeded contact' : null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }
        DB::table('client_contacts')->insert($contactRows);

        $meetingCount = min(200, max(100, (int) floor($clientCount * 0.1)));
        $meetingRows = [];
        foreach (range(1, $meetingCount) as $index) {
            $clientId = $clientIds[$index % count($clientIds)];
            $meetingRows[] = [
                'client_id' => $clientId,
                'user_id' => $salesUser?->id,
                'meeting_date' => now()->subDays($index % 30)->toDateString(),
                'title' => 'Stress follow up '.$index,
                'outcome' => ['prospecting', 'follow_up', 'negotiation', 'closed_won'][$index % 4],
                'notes' => 'Seeded meeting log for performance dataset',
                'next_follow_up_at' => now()->addDays(($index % 14) + 1),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }
        DB::table('meeting_logs')->insert($meetingRows);

        $vehicles = collect();
        foreach (range(1, 20) as $index) {
            $status = match (true) {
                $index >= 17 && $index <= 18 => 'po',
                $index === 19 => 'maintenance',
                $index === 20 => 'hold',
                default => 'available',
            };

            $vehicles->push(Vehicle::factory()->create([
                'pool_id' => $pools[($index - 1) % 3]->id,
                'status' => $status,
            ]));
        }

        $drivers = collect();
        foreach (range(1, 12) as $index) {
            $drivers->push(Driver::factory()->create([
                'pool_id' => $pools[($index - 1) % 3]->id,
                'status' => match ($index) {
                    10 => 'sick',
                    11 => 'on_leave',
                    12 => 'inactive',
                    default => 'active',
                },
                'license_expired_at' => match ($index) {
                    1 => now()->subDays(3)->toDateString(),
                    2 => now()->addDays(15)->toDateString(),
                    default => now()->addMonths(8)->toDateString(),
                },
            ]));
        }

        foreach (range(0, 11) as $index) {
            DriverAttendance::factory()->create([
                'driver_id' => $drivers[$index]->id,
                'attendance_date' => today()->subDays($index % 5),
                'status' => match ($index) {
                    9 => 'sick',
                    10 => 'leave',
                    11 => 'absent',
                    default => 'present',
                },
            ]);
        }

        $bookings = collect();
        $bookingStatuses = array_merge(
            array_fill(0, 20, 'pending'),
            array_fill(0, 15, 'assigned'),
            array_fill(0, 15, 'confirmed'),
            array_fill(0, 10, 'completed'),
        );

        foreach ($bookingStatuses as $index => $status) {
            $vehicle = in_array($status, ['assigned', 'confirmed', 'completed'], true)
                ? $vehicles[$index % 16]
                : null;
            $driver = in_array($status, ['assigned', 'confirmed', 'completed'], true)
                ? $drivers[$index % 9]
                : null;

            $booking = Booking::factory()->create([
                'booking_number' => 'BK-'.now()->format('Ym').'-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'client_id' => $clientIds[$index % count($clientIds)],
                'requested_by' => $salesUser?->id,
                'pool_id' => $pools[$index % 3]->id,
                'vehicle_id' => $vehicle?->id,
                'driver_id' => $driver?->id,
                'status' => $status,
                'start_datetime' => now()->addDays(($index % 20) + 1)->addHours($index % 6),
                'end_datetime' => now()->addDays(($index % 20) + 1)->addHours(($index % 6) + 4),
            ]);

            if ($driver && $vehicle) {
                DriverAssignment::factory()->create([
                    'booking_id' => $booking->id,
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => $driver->id,
                    'assigned_by' => $headPoolUser?->id,
                ]);
            }

            $bookings->push($booking);
        }

        $this->seedMaintenanceLogs($vehicles, $operationUser?->id, 8);
        $purchaseOrders = $this->seedPurchaseOrders($bookings, $financeUser?->id, 45, 40);
        $invoices = $this->seedInvoices($purchaseOrders, 40);
        $vouchers = $this->seedEVouchers(Client::query()->take(8)->get(), 8);
        $this->seedPayments($invoices, $vouchers, $financeUser?->id, 36);
    }

    protected function seedMaintenanceLogs(Collection $vehicles, ?int $reportedBy, int $count = 6): void
    {
        foreach (range(1, $count) as $index) {
            $status = match ($index % 6) {
                1 => 'in_progress',
                2 => 'scheduled',
                3, 4 => 'completed',
                default => 'cancelled',
            };

            $vehicle = $vehicles[min($vehicles->count() - 1, 10 + (($index - 1) % max(1, $vehicles->count() - 10)))];

            MaintenanceLog::factory()->create([
                'vehicle_id' => $vehicle->id,
                'reported_by' => $reportedBy,
                'status' => $status,
                'title' => 'Maintenance #'.$index,
            ]);

            if ($status === 'in_progress') {
                $vehicle->update(['status' => 'maintenance']);
            } elseif ($vehicle->status === 'maintenance') {
                $vehicle->update(['status' => 'available']);
            }
        }
    }

    protected function seedPurchaseOrders(Collection $bookings, ?int $approvedBy, int $count, int $invoicedCount): Collection
    {
        $eligibleBookings = $bookings
            ->whereIn('status', ['confirmed', 'completed'])
            ->values();

        $purchaseOrders = collect();
        foreach (range(1, min($count, $eligibleBookings->count())) as $index) {
            $booking = $eligibleBookings[$index - 1];
            $poStatus = $index <= $invoicedCount
                ? 'invoiced'
                : ($index <= $count - 5 ? 'approved' : 'pending');
            $subtotal = 1000000 + ($index * 100000);
            $tax = round($subtotal * 0.11, 2);

            $purchaseOrders->push(PurchaseOrder::query()->create([
                'po_number' => 'PO-'.now()->format('Ym').'-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'booking_id' => $booking->id,
                'client_id' => $booking->client_id,
                'status' => $poStatus,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
                'approved_by' => in_array($poStatus, ['approved', 'invoiced'], true) ? $approvedBy : null,
                'approved_at' => in_array($poStatus, ['approved', 'invoiced'], true) ? now() : null,
            ]));
        }

        return $purchaseOrders;
    }

    protected function seedInvoices(Collection $purchaseOrders, int $count): Collection
    {
        $statusSequence = ['partial', 'paid', 'overdue', 'sent', 'sent', 'partial', 'draft', 'draft'];
        $invoices = collect();

        foreach (range(1, min($count, $purchaseOrders->count())) as $index) {
            $po = $purchaseOrders[$index - 1];
            $status = $statusSequence[($index - 1) % count($statusSequence)];

            $invoices->push(Invoice::query()->create([
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
            ]));
        }

        return $invoices;
    }

    protected function seedEVouchers(Collection $clients, int $count = 5): Collection
    {
        $clientCollection = $clients->values();

        return collect(range(1, $count))->map(function (int $index) use ($clientCollection) {
            $status = match ($index) {
                1 => 'active',
                2 => 'used',
                3 => 'expired',
                4 => 'active',
                5 => 'cancelled',
                default => $index % 2 === 0 ? 'active' : 'used',
            };

            $amount = 250000 + ($index * 100000);
            $usedAmount = match ($status) {
                'used' => $amount,
                default => $index === 4 ? 100000 : 0,
            };

            return EVoucher::factory()->create([
                'code' => 'EV-'.now()->format('Ym').'-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'client_id' => $index === 4 ? null : $clientCollection[($index - 1) % max(1, $clientCollection->count())]?->id,
                'status' => $status,
                'amount' => $amount,
                'used_amount' => $usedAmount,
                'expired_at' => match ($status) {
                    'expired' => now()->subDay()->toDateString(),
                    default => now()->addDays(15 + ($index * 5))->toDateString(),
                },
                'used_at' => $status === 'used' ? now()->subDay() : null,
            ]);
        });
    }

    protected function seedPayments(Collection $invoices, Collection $vouchers, ?int $createdBy, int $count): void
    {
        foreach (range(1, min($count, $invoices->count())) as $index) {
            $invoice = $invoices[$index - 1];
            $amount = match ($invoice->status) {
                'partial' => round((float) $invoice->total / 2, 2),
                'paid' => (float) $invoice->total,
                default => round((float) $invoice->total / 4, 2),
            };

            $method = $index === 6 ? 'evoucher' : 'bank_transfer';
            $voucher = $method === 'evoucher' ? $vouchers->firstWhere('status', 'active') : null;

            if ($voucher) {
                $voucher->used_amount = (float) $voucher->used_amount + $amount;
                if ($voucher->used_amount >= (float) $voucher->amount) {
                    $voucher->status = 'used';
                    $voucher->used_at = now();
                }
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
                'created_by' => $createdBy,
            ]);
        }
    }

    protected function demoSeedEnabled(): bool
    {
        $explicit = $this->envBool('ENABLE_DEMO_SEED');

        if (app()->environment('production')) {
            return $explicit ?? false;
        }

        return $explicit ?? true;
    }

    protected function seedMode(): string
    {
        $mode = strtolower((string) env('DEMO_SEED_MODE', 'demo'));

        return in_array($mode, ['demo', 'stress'], true) ? $mode : 'demo';
    }

    protected function demoCustomerCount(): int
    {
        return max(1000, $this->envInt('DEMO_CUSTOMER_COUNT', 1200));
    }

    protected function stressCustomerCount(): int
    {
        return max(1000, $this->envInt('DEMO_CUSTOMER_COUNT', 1200));
    }

    protected function envBool(string $key): ?bool
    {
        $value = env($key);

        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
    }

    protected function envInt(string $key, int $default): int
    {
        $value = env($key);

        if ($value === null || $value === '') {
            return $default;
        }

        return (int) $value;
    }
}
