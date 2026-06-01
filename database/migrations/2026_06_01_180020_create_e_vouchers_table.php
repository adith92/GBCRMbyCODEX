<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e_vouchers', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->decimal('amount', 15, 2);
            $table->decimal('used_amount', 15, 2)->default(0);
            $table->date('expired_at')->nullable();
            $table->dateTime('used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_vouchers');
    }
};
