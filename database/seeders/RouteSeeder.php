<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get cities
        $kathmandu = City::where('name', 'Kathmandu')->first();
        $pokhara = City::where('name', 'Pokhara')->first();
        $chitwan = City::where('name', 'Chitwan')->first();
        $biratnagar = City::where('name', 'Biratnagar')->first();
        $birgunj = City::where('name', 'Birgunj')->first();
        $butwal = City::where('name', 'Butwal')->first();
        $dhangadhi = City::where('name', 'Dhangadhi')->first();
        $nepalgunj = City::where('name', 'Nepalgunj')->first();

        $routes = [
            // Kathmandu routes
            [
                'from_city_id' => $kathmandu->id,
                'to_city_id' => $pokhara->id,
                'estimated_km' => 200.5,
                'estimated_time' => '06:00:00',
            ],
            [
                'from_city_id' => $kathmandu->id,
                'to_city_id' => $chitwan->id,
                'estimated_km' => 146.2,
                'estimated_time' => '04:30:00',
            ],
            [
                'from_city_id' => $kathmandu->id,
                'to_city_id' => $biratnagar->id,
                'estimated_km' => 543.8,
                'estimated_time' => '12:00:00',
            ],
            [
                'from_city_id' => $kathmandu->id,
                'to_city_id' => $birgunj->id,
                'estimated_km' => 135.4,
                'estimated_time' => '04:00:00',
            ],
            [
                'from_city_id' => $kathmandu->id,
                'to_city_id' => $butwal->id,
                'estimated_km' => 276.3,
                'estimated_time' => '07:30:00',
            ],
            [
                'from_city_id' => $kathmandu->id,
                'to_city_id' => $nepalgunj->id,
                'estimated_km' => 516.7,
                'estimated_time' => '11:00:00',
            ],
            [
                'from_city_id' => $kathmandu->id,
                'to_city_id' => $dhangadhi->id,
                'estimated_km' => 678.9,
                'estimated_time' => '14:00:00',
            ],

            // Return routes (reverse direction)
            [
                'from_city_id' => $pokhara->id,
                'to_city_id' => $kathmandu->id,
                'estimated_km' => 200.5,
                'estimated_time' => '06:00:00',
            ],
            [
                'from_city_id' => $chitwan->id,
                'to_city_id' => $kathmandu->id,
                'estimated_km' => 146.2,
                'estimated_time' => '04:30:00',
            ],
            [
                'from_city_id' => $biratnagar->id,
                'to_city_id' => $kathmandu->id,
                'estimated_km' => 543.8,
                'estimated_time' => '12:00:00',
            ],

            // Inter-city routes
            [
                'from_city_id' => $pokhara->id,
                'to_city_id' => $chitwan->id,
                'estimated_km' => 125.6,
                'estimated_time' => '03:30:00',
            ],
            [
                'from_city_id' => $chitwan->id,
                'to_city_id' => $pokhara->id,
                'estimated_km' => 125.6,
                'estimated_time' => '03:30:00',
            ],
        ];

        foreach ($routes as $route) {
            Route::create($route);
        }
    }
}
