<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $arrayOfRoleNames = [
            ['name' => 'super-admin'],
            ['name' => 'admin'],
            ['name' => 'editor'],
            ['name' => 'viewer'],
            ['name' => 'draftsman'],
        ];

        $roles = collect($arrayOfRoleNames)->map(function ($role) {
            return [
                'name' => $role['name'],
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        Role::query()->insert($roles->toArray());
    }
}
