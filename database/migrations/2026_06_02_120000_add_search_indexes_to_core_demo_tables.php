<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->index('name', 'clients_name_index');
            $table->index('legal_name', 'clients_legal_name_index');
            $table->index('status', 'clients_status_index');
            $table->index('tier', 'clients_tier_index');
        });

        Schema::table('drivers', function (Blueprint $table): void {
            $table->index('name', 'drivers_name_index');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table): void {
            $table->dropIndex('clients_name_index');
            $table->dropIndex('clients_legal_name_index');
            $table->dropIndex('clients_status_index');
            $table->dropIndex('clients_tier_index');
        });

        Schema::table('drivers', function (Blueprint $table): void {
            $table->dropIndex('drivers_name_index');
        });
    }
};
