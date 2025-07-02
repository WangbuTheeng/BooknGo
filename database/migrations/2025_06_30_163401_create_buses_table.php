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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained()->onDelete('cascade');
            $table->string('registration_number', 50)->unique();
            $table->string('name', 100)->nullable();
            $table->enum('type', ['AC', 'Deluxe', 'Normal', 'Sleeper'])->default('Normal');
            $table->unsignedSmallInteger('total_seats')->nullable();
            $table->json('layout_config')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
