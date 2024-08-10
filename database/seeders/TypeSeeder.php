<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
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
                'key' => 'column1',
                'label' => 'Hero section column 1',
            ],
            [
                'key' => 'column2',
                'label' => 'Hero section column 2',
            ],
            [
                'key' => 'column3',
                'label' => 'Hero section column 3',
            ],
            [
                'key' => 'column4',
                'label' => 'Hero section column 4',
            ],
            [
                'key' => 'featured',
                'label' => 'Category Lead',
            ],
            [
                'key' => 'displayed',
                'label' => 'Category Display',
            ],
            [
                'key' => 'general',
                'label' => 'General',
            ],
        ];
        foreach ($collections as $collection) {
            $type = new Type();
            $type->fill($collection);
            $type->save();
        }
    }
}
