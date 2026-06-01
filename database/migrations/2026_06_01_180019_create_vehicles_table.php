<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pool_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('plate_number')->unique();
            $table->enum('product_line', ['goldenbird', 'bigbird', 'cititrans', 'regular']);
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('year')->nullable();
            $table->unsignedInteger('seat_capacity')->nullable();
            $table->enum('status', ['available', 'po', 'maintenance', 'hold'])->default('available');
            $table->unsignedBigInteger('odometer')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
