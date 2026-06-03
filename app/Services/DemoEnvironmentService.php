<?php

namespace App\Services;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DemoEnvironmentService
{
    public function resetDemoData(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'report_snapshots',
            'payments',
            'invoices',
            'e_vouchers',
            'purchase_orders',
            'driver_assignments',
            'bookings',
            'maintenance_logs',
            'meeting_logs',
            'client_contacts',
            'driver_attendances',
            'vendor_partners',
            'vehicles',
            'drivers',
            'clients',
            'pools',
        ] as $table) {
            DB::table($table)->delete();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Artisan::call('db:seed', ['--class' => DemoDataSeeder::class, '--force' => true]);
    }
}
