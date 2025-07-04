<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Seat;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Seat::chunk(100, function ($seats) {
            foreach ($seats as $seat) {
                $seat->update([
                    'position' => ($seat->seat_number % 4 === 1 || $seat->seat_number % 4 === 2) ? 'left' : 'right',
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
