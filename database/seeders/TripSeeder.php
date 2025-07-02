<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\Bus;
use App\Models\Route;
use Carbon\Carbon;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buses = Bus::all();
        $routes = Route::all();

        if ($buses->isEmpty() || $routes->isEmpty()) {
            return;
        }

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        foreach ($buses as $bus) {
            foreach ($routes as $route) {
                // Create trips for today and tomorrow
                for ($day = 0; $day < 2; $day++) {
                    $date = $today->copy()->addDays($day);
                    
                    // Morning trip
                    Trip::create([
                        'bus_id' => $bus->id,
                        'route_id' => $route->id,
                        'departure_datetime' => $date->copy()->setTime(6, 0),
                        'arrival_time' => $date->copy()->setTime(12, 0),
                        'price' => 1500.00,
                        'status' => 'active',
                    ]);

                    // Evening trip
                    Trip::create([
                        'bus_id' => $bus->id,
                        'route_id' => $route->id,
                        'departure_datetime' => $date->copy()->setTime(18, 0),
                        'arrival_time' => $date->copy()->addDay()->setTime(0, 0),
                        'price' => 1500.00,
                        'status' => 'active',
                    ]);
                }
            }
        }
    }
}
