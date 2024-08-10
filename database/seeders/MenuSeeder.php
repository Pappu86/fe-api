<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::query()->delete();
        $menus = [
            [
                'title' => 'Home',
                'icon' => 'mdi-home',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Sliders',
                        'icon' => 'mdi-label',
                        'link' => '/home/sliders',
                        'status' => 1,
                    ],
                ]
            ],
            [
                'title' => 'Contents',
                'icon' => 'mdi-home',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Posts',
                        'icon' => 'mdi-format-list-bulleted-square',
                        'link' => '/contents/posts',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Just In Posts',
                        'icon' => 'mdi-format-list-bulleted-square',
                        'link' => '/contents/latest-posts',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Live Media',
                        'icon' => 'mdi-video',
                        'link' => '/contents/media',
                        'status' => 1,
                    ],
                ]
            ],
            [
                'title' => 'Advertisements',
                'icon' => 'mdi-advertisements',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Advertisements',
                        'icon' => 'mdi-advertisements',
                        'link' => '/ads/advertisements',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Positions',
                        'icon' => 'mdi-book-open-page-variant',
                        'link' => '/ads/positions',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Providers',
                        'icon' => 'mdi-account-cash',
                        'link' => '/ads/providers',
                        'status' => 1,
                    ],
                ]
            ],
            [
                'title' => 'Common',
                'icon' => 'mdi-home',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Categories',
                        'icon' => 'mdi-head-snowflake',
                        'link' => '/common/categories',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Types',
                        'icon' => 'mdi-fire',
                        'link' => '/common/types',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Tags',
                        'icon' => 'mdi-fire',
                        'link' => '/common/tags',
                        'status' => 1,
                    ],
                ]
            ],
            [
                'title' => 'Media Library',
                'icon' => 'mdi-home',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Assets',
                        'icon' => 'mdi-file',
                        'link' => '/media/assets',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Category',
                        'icon' => 'mdi-folder',
                        'link' => '/media/categories',
                        'status' => 1,
                    ],
                ]
            ],
            [
                'title' => 'Newsletter',
                'icon' => 'mdi-home',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Subscribers',
                        'icon' => 'mdi-account-multiple',
                        'link' => '/subscribers',
                        'status' => 1,
                    ],
                ]
            ],
            [
                'title' => 'Users',
                'icon' => 'mdi-account-multiple',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Users',
                        'icon' => 'mdi-account-multiple',
                        'link' => '/users',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Reporters',
                        'icon' => 'mdi-account-multiple',
                        'link' => '/reporters',
                        'status' => 1,
                    ],
                ]
            ],
            [
                'title' => 'Roles',
                'icon' => 'mdi-lock',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Roles',
                        'icon' => 'mdi-lock',
                        'link' => '/roles',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Role wise Permissions',
                        'icon' => 'mdi-lock',
                        'link' => '/role-permissions',
                        'status' => 1,
                    ]
                ]
            ],
            [
                'title' => 'Menus',
                'icon' => 'mdi-menu',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Backend Menus',
                        'icon' => 'mdi-menu',
                        'link' => '/menus/backend-menus',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Frontend Top Menus',
                        'icon' => 'mdi-menu',
                        'link' => '/menus/top-menus',
                        'status' => 1,
                    ],
                ]
            ],
            [
                'title' => 'Administrations',
                'icon' => 'mdi-lock',
                'link' => null,
                'status' => 1,
                'children' => [
                    [
                        'title' => 'Activities Log',
                        'icon' => 'mdi-history',
                        'link' => '/administrations/activity-log',
                        'status' => 1,
                    ],
                    [
                        'title' => 'Users Log',
                        'icon' => 'mdi-history',
                        'link' => '/administrations/user-log',
                        'status' => 1,
                    ],
                ]
            ],
        ];

        foreach ($menus as $key => $item) {
            $menu = new Menu();
            $menu->fill([
                'title' => $item['title'],
                'status' => $item['status'],
                'icon' => $item['icon'],
                'ordering' => $key
            ]);
            $menu->save();
            foreach ($item['children'] as $k => $child) {
                $children = new Menu();
                $children->fill([
                    'title' => $child['title'],
                    'status' => $child['status'],
                    'icon' => $child['icon'],
                    'link' => $child['link'],
                    'ordering' => $k,
                    'parent_id' => $menu->id,
                ]);
                $children->save();
            }
        }

        $items = Menu::all();
        foreach ($items as $item) {
            $item->roles()->attach([1]);
        }
    }
}
