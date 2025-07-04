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
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('trip_id')->constrained()->onDelete('cascade');
                $table->char('booking_code', 10)->unique();
                $table->string('booking_reference', 20)->unique();
                $table->string('passenger_name');
                $table->string('passenger_phone', 20);
                $table->string('passenger_email')->nullable();
                $table->enum('status', ['booked', 'cancelled'])->default('booked');
                $table->decimal('total_amount', 10, 2);
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
                $table->string('cancellation_reason')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
