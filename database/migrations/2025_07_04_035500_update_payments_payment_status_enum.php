<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the payment_status enum to include 'completed'
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'success', 'failed', 'refunded', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending'");
    }
};
