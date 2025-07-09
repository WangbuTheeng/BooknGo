<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('seats')) {
            Schema::create('seats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bus_id')->constrained()->onDelete('cascade');
                $table->string('seat_number', 10);
                $table->string('position', 20)->nullable();
                $table->enum('seat_type', ['passenger', 'vip', 'blocked', 'conductor', 'driver'])->default('passenger');
                $table->unsignedTinyInteger('row_number')->nullable();
                $table->unsignedTinyInteger('column_number')->nullable();
                $table->string('side', 10)->nullable(); // 'left', 'right', 'center'
                $table->boolean('is_available_for_booking')->default(true);
                $table->decimal('price_multiplier', 3, 2)->default(1.00); // For VIP seats pricing
                $table->json('properties')->nullable(); // Additional seat properties
                $table->timestamps();

                $table->unique(['bus_id', 'seat_number']);
                $table->index(['bus_id', 'row_number', 'column_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
