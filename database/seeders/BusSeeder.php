<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Seat;
use App\Models\Operator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operators = Operator::all();

        $buses = [
            [
                'operator_id' => $operators->first()->id,
                'registration_number' => 'BA-1-CHA-1234',
                'name' => 'Greenline Express',
                'type' => 'AC',
                'total_seats' => 32,
                'features' => ['AC', 'WiFi', 'Entertainment'],
            ],
            [
                'operator_id' => $operators->first()->id,
                'registration_number' => 'BA-2-CHA-5678',
                'name' => 'Greenline Deluxe',
                'type' => 'Deluxe',
                'total_seats' => 40,
                'features' => ['Reclining Seats', 'Reading Light'],
            ],
            [
                'operator_id' => $operators->last()->id,
                'registration_number' => 'BA-3-CHA-9012',
                'name' => 'Buddha Air Express',
                'type' => 'AC',
                'total_seats' => 36,
                'features' => ['AC', 'USB Charging', 'Blanket'],
            ],
            [
                'operator_id' => $operators->last()->id,
                'registration_number' => 'BA-4-CHA-3456',
                'name' => 'Buddha Air Normal',
                'type' => 'Normal',
                'total_seats' => 45,
                'features' => ['Basic Seating'],
            ],
        ];

        foreach ($buses as $busData) {
            $bus = Bus::create($busData);

            // Generate seats for each bus
            $this->generateSeats($bus);
        }
    }

    /**
     * Generate seats for a bus
     */
    private function generateSeats(Bus $bus)
    {
        for ($i = 1; $i <= $bus->total_seats; $i++) {
            Seat::create([
                'bus_id' => $bus->id,
                'seat_number' => (string) $i,
                'position' => $this->calculateSeatPosition($i),
            ]);
        }
    }

    /**
     * Calculate seat position based on seat number
     */
    private function calculateSeatPosition(int $seatNumber): string
    {
        // Simple logic: assume 4 seats per row (2+2 configuration)
        $row = ceil($seatNumber / 4);
        $position = ($seatNumber - 1) % 4;

        $positions = ['WL', 'AL', 'AR', 'WR']; // Window-Left, Aisle-Left, Aisle-Right, Window-Right

        return "R{$row}-{$positions[$position]}";
    }
}
