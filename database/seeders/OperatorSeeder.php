<?php

namespace Database\Seeders;

use App\Models\Operator;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get operator users
        $greenlineUser = User::where('email', 'greenline@bookngo.com')->first();
        $buddhaUser = User::where('email', 'buddha@bookngo.com')->first();

        if ($greenlineUser) {
            Operator::create([
                'user_id' => $greenlineUser->id,
                'company_name' => 'Greenline Tours',
                'license_number' => 'GL001',
                'contact_info' => [
                    'phone' => '01-4444444',
                    'email' => 'info@greenlinetours.com',
                    'website' => 'www.greenlinetours.com'
                ],
                'address' => 'Kathmandu, Nepal',
                'verified' => true,
            ]);
        }

        if ($buddhaUser) {
            Operator::create([
                'user_id' => $buddhaUser->id,
                'company_name' => 'Buddha Air Transport',
                'license_number' => 'BA001',
                'contact_info' => [
                    'phone' => '01-5555555',
                    'email' => 'info@buddhaair.com',
                    'website' => 'www.buddhaair.com'
                ],
                'address' => 'Pokhara, Nepal',
                'verified' => true,
            ]);
        }
    }
}
