<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->command->info('Roles table seeded!');
        $this->call(PermissionSeeder::class);
        $this->command->info('Permissions table seeded!');
        $this->call(UserSeeder::class);
        $this->command->info('Users Table Seeded!');
        $this->call(MenuSeeder::class);
        $this->command->info('Menus Table Seeded!');
    }
}
