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
        // Update the payments table to include Stripe in the method enum
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('method', ['eSewa', 'Khalti', 'Cash', 'Stripe'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('method', ['eSewa', 'Khalti', 'Cash'])->change();
        });
    }
};
