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
        Schema::table('buses', function (Blueprint $table) {
            // Layout configuration fields
            $table->enum('layout_pattern', ['2x2', '2x1', '1x1', 'custom'])->default('2x2')->after('total_seats');
            $table->unsignedTinyInteger('rows_count')->nullable()->after('layout_pattern');
            $table->unsignedTinyInteger('seats_per_row')->nullable()->after('rows_count');
            $table->unsignedTinyInteger('back_row_seats')->nullable()->after('seats_per_row');
            $table->boolean('has_driver_side_seat')->default(false)->after('back_row_seats');
            $table->boolean('driver_side_seat_usable')->default(true)->after('has_driver_side_seat');
            $table->boolean('has_conductor_area')->default(false)->after('driver_side_seat_usable');
            $table->enum('bus_category', ['standard', 'deluxe', 'sleeper', 'semi_sleeper', 'vip'])->default('standard')->after('has_conductor_area');
            $table->json('layout_metadata')->nullable()->after('bus_category'); // Store additional layout info
            $table->boolean('layout_configured')->default(false)->after('layout_metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->dropColumn([
                'layout_pattern',
                'rows_count',
                'seats_per_row',
                'back_row_seats',
                'has_driver_side_seat',
                'driver_side_seat_usable',
                'has_conductor_area',
                'bus_category',
                'layout_metadata',
                'layout_configured'
            ]);
        });
    }
};
