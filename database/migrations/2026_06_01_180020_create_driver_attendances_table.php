<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'sick', 'leave']);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['driver_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_attendances');
    }
};
