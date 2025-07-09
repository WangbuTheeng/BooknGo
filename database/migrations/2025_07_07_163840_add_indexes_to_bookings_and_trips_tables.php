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
            if (!Schema::hasIndex('bookings', ['status'])) {
                $table->index('status');
            }
            if (!Schema::hasIndex('bookings', ['payment_status'])) {
                $table->index('payment_status');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasIndex('trips', ['departure_datetime'])) {
                $table->index('departure_datetime');
            }
            if (!Schema::hasIndex('trips', ['status'])) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_index');
            $table->dropIndex('bookings_payment_status_index');
        });

        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex('trips_departure_datetime_index');
            $table->dropIndex('trips_status_index');
        });
    }
};
