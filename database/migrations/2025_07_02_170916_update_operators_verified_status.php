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
        // Update all existing operators to be verified
        DB::table('operators')->update(['verified' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert all operators to unverified
        DB::table('operators')->update(['verified' => false]);
    }
};
