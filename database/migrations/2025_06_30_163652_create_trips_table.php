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
        if (!Schema::hasTable('trips')) {
            Schema::create('trips', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bus_id')->constrained()->onDelete('cascade');
                $table->foreignId('route_id')->constrained()->onDelete('cascade');
                $table->datetime('departure_datetime');
                $table->datetime('arrival_time')->nullable();
                $table->decimal('price', 10, 2);
                $table->boolean('is_festival_fare')->default(false);
                $table->enum('status', ['active', 'cancelled'])->default('active');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
