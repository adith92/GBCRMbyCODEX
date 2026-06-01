<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pool_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('employee_code')->unique()->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('license_type')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expired_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'sick', 'on_leave'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
