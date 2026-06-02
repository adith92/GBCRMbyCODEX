<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_assignments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('driver_id');
            $table->enum('assignment_type', ['primary', 'substitute', 'temporary'])->default('primary');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->text('reason')->nullable();
            $table->dateTime('assigned_at');
            $table->dateTime('released_at')->nullable();
            $table->timestamps();

            $table->index('booking_id');
            $table->index('vehicle_id');
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_assignments');
    }
};
