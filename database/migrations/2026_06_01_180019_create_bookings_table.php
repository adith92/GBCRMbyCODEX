<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->string('booking_number')->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('pool_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->text('pickup_location')->nullable();
            $table->text('destination')->nullable();
            $table->enum('status', ['pending', 'assigned', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
