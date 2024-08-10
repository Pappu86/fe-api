<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
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
        Permission::query()->delete();

        $arrayOfPermissionNames = [
            "viewAny user", "view user", "create user", "update user", "delete user",
            "viewAny role", "view role", "create role", "update role", "delete role",
            "viewAny menu", "view menu", "create menu", "update menu", "delete menu",
            "viewAny setting", "view setting", "create setting", "update setting", "delete setting",
            "viewAny user-log", "view user-log", "delete user-log",
            "viewAny activity", "view activity", "delete activity",
            "viewAny subscriber", "view subscriber", "create subscriber", "update subscriber", "delete subscriber",
            "viewAny category", "view category", "create category", "update category", "delete category",
            "viewAny type", "view type", "create type", "update type", "delete type",
            "viewAny tag", "view tag", "create tag", "update tag", "delete tag",
            "viewAny reporter", "view reporter", "create reporter", "update reporter", "delete reporter",
            "viewAny post", "view post", "create post", "update post", "delete post", "publish post",
            "viewAny slider", "view slider", "create slider", "update slider", "delete slider",
            "viewAny latest-post", "view latest-post", "create latest-post", "update latest-post", "delete latest-post",
            "viewAny media", "view media", "create media", "update media", "delete media",
            "viewAny advertisement", "view advertisement", "create advertisement", "update advertisement", "delete advertisement",
            "viewAny asset_category", "view asset_category", "create asset_category", "update asset_category", "delete asset_category",
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        Permission::query()->insert($permissions->toArray());

//        $defaultSubscriberPermissions = [];
//        $role = Role::whereName('subscriber')->first();
//        $role->givePermissionTo($defaultSubscriberPermissions);
    }
}
