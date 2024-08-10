<?php

namespace Database\Seeders;

use App\Models\AdsProvider;
use Illuminate\Database\Seeder;

class AdsProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $collections = [
            [
                'status' => 1,
                'name' => 'Nagad',
            ],
            [
                'status' => 1,
                'name' => 'Rocket',
            ],
            [
                'status' => 1,
                'name' => 'Ispahani',
            ],
        ];
        foreach ($collections as $collection) {
            $provider = new AdsProvider();
            $provider->fill($collection);
            $provider->save();
        }
    }
}
