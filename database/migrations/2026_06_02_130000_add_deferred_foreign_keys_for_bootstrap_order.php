<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->usesSqlite()) {
            return;
        }

        Schema::table('client_contacts', function (Blueprint $table): void {
            if (! $this->foreignKeyExists('client_contacts', 'client_contacts_client_id_foreign')) {
                $table->foreign('client_id')->references('id')->on('clients')->cascadeOnUpdate()->cascadeOnDelete();
            }
        });

        Schema::table('bookings', function (Blueprint $table): void {
            if (! $this->foreignKeyExists('bookings', 'bookings_vehicle_id_foreign')) {
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnUpdate()->nullOnDelete();
            }

            if (! $this->foreignKeyExists('bookings', 'bookings_driver_id_foreign')) {
                $table->foreign('driver_id')->references('id')->on('drivers')->cascadeOnUpdate()->nullOnDelete();
            }
        });

        Schema::table('driver_assignments', function (Blueprint $table): void {
            if (! $this->foreignKeyExists('driver_assignments', 'driver_assignments_booking_id_foreign')) {
                $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnUpdate()->cascadeOnDelete();
            }

            if (! $this->foreignKeyExists('driver_assignments', 'driver_assignments_vehicle_id_foreign')) {
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnUpdate()->restrictOnDelete();
            }

            if (! $this->foreignKeyExists('driver_assignments', 'driver_assignments_driver_id_foreign')) {
                $table->foreign('driver_id')->references('id')->on('drivers')->cascadeOnUpdate()->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        if ($this->usesSqlite()) {
            return;
        }

        Schema::table('driver_assignments', function (Blueprint $table): void {
            if ($this->foreignKeyExists('driver_assignments', 'driver_assignments_booking_id_foreign')) {
                $table->dropForeign('driver_assignments_booking_id_foreign');
            }

            if ($this->foreignKeyExists('driver_assignments', 'driver_assignments_vehicle_id_foreign')) {
                $table->dropForeign('driver_assignments_vehicle_id_foreign');
            }

            if ($this->foreignKeyExists('driver_assignments', 'driver_assignments_driver_id_foreign')) {
                $table->dropForeign('driver_assignments_driver_id_foreign');
            }
        });

        Schema::table('bookings', function (Blueprint $table): void {
            if ($this->foreignKeyExists('bookings', 'bookings_vehicle_id_foreign')) {
                $table->dropForeign('bookings_vehicle_id_foreign');
            }

            if ($this->foreignKeyExists('bookings', 'bookings_driver_id_foreign')) {
                $table->dropForeign('bookings_driver_id_foreign');
            }
        });

        Schema::table('client_contacts', function (Blueprint $table): void {
            if ($this->foreignKeyExists('client_contacts', 'client_contacts_client_id_foreign')) {
                $table->dropForeign('client_contacts_client_id_foreign');
            }
        });
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $database)
            ->where('table_name', $table)
            ->where('constraint_name', $constraint)
            ->exists();
    }

    private function usesSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }
};
