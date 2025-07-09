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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasIndex('bookings', ['trip_id'])) {
                $table->index('trip_id');
            }
            if (!Schema::hasIndex('bookings', ['expires_at'])) {
                $table->index('expires_at');
            }
        });

        Schema::table('booking_seats', function (Blueprint $table) {
            $table->index('seat_id');
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['trip_id']);
            $table->dropIndex(['expires_at']);
        });

        Schema::table('booking_seats', function (Blueprint $table) {
            $table->dropIndex(['seat_id']);
            $table->dropIndex(['booking_id']);
        });
    }
};
