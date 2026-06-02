<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_contacts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_contacts');
    }
};
