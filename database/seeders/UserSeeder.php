<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super_admin1 = User::query()->create([
            'name' => 'Eliyas Hossain',
            'status' => true,
            'email' => 'eliyas@batterylowinteractive.com',
            'mobile' => '+8801827848374',
            'password' => Hash::make('ILoveBatteryLow'),
            'email_verified_at' => now(),
            'mobile_verified_at' => now(),
        ]);

        $super_admin1->assignRole('super-admin');
    }
}
