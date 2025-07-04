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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users')->after('payment_status');
            $table->foreignId('confirmed_by_user_id')->nullable()->constrained('users')->after('processed_by_user_id');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['processed_by_user_id']);
            $table->dropForeign(['confirmed_by_user_id']);
            $table->dropColumn(['processed_by_user_id', 'confirmed_by_user_id', 'confirmed_at']);
        });
    }
};
