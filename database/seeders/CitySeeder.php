<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Province 1 (Koshi)
            ['name' => 'Kathmandu', 'province' => 'Bagmati', 'region' => 'Central'],
            ['name' => 'Pokhara', 'province' => 'Gandaki', 'region' => 'Western'],
            ['name' => 'Chitwan', 'province' => 'Bagmati', 'region' => 'Central'],
            ['name' => 'Biratnagar', 'province' => 'Koshi', 'region' => 'Eastern'],
            ['name' => 'Birgunj', 'province' => 'Madhesh', 'region' => 'Central'],
            ['name' => 'Dharan', 'province' => 'Koshi', 'region' => 'Eastern'],
            ['name' => 'Butwal', 'province' => 'Lumbini', 'region' => 'Western'],
            ['name' => 'Hetauda', 'province' => 'Bagmati', 'region' => 'Central'],
            ['name' => 'Dhangadhi', 'province' => 'Sudurpashchim', 'region' => 'Far-Western'],
            ['name' => 'Mahendranagar', 'province' => 'Sudurpashchim', 'region' => 'Far-Western'],
            ['name' => 'Janakpur', 'province' => 'Madhesh', 'region' => 'Central'],
            ['name' => 'Nepalgunj', 'province' => 'Lumbini', 'region' => 'Mid-Western'],
            ['name' => 'Bhairahawa', 'province' => 'Lumbini', 'region' => 'Western'],
            ['name' => 'Itahari', 'province' => 'Koshi', 'region' => 'Eastern'],
            ['name' => 'Gorkha', 'province' => 'Gandaki', 'region' => 'Western'],
            ['name' => 'Palpa', 'province' => 'Lumbini', 'region' => 'Western'],
            ['name' => 'Baglung', 'province' => 'Gandaki', 'region' => 'Western'],
            ['name' => 'Dang', 'province' => 'Lumbini', 'region' => 'Mid-Western'],
            ['name' => 'Surkhet', 'province' => 'Karnali', 'region' => 'Mid-Western'],
            ['name' => 'Jumla', 'province' => 'Karnali', 'region' => 'Mid-Western'],
        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
