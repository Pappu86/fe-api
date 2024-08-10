<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
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
                'ordering' => 0,
                'name' => 'Economy',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Bangladesh',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Global',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Budget-2019-20',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Budget-2020-21',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Stock',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Bangladesh',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Global',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Trade',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'National',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Politics',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Country',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Law & Order',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'World',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'America',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Asia/South Asia',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Europe',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Africa',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Views',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Views',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Reviews',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Opinions',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Columns',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Analysis',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Letters',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Economic Trends and Insights',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Editorial',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Education',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Article',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Media',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Sci-Tech',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Health',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Sports',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'World Cup 2019',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'T20 World Cup 2021',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Cricket',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Football',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'More Sports',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Entertainment',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Others',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Special Issues',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Power & Energy',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'RMG & Textile',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Power & Energy 2',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'RMG & Textile 4',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'RMG & Textile 5',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Amar Ekushey Special 2018',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Special on Independence Day',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Pahela Baishakh 1425',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'special on National Mourning Day 2018',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Special on National Mourning Day',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Remembering Moazzem Hossain',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'PABL-FE Roundtable on Insurance Industry\'s Expectations and Attainments in the Budget 2019-2020',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'PABL-FE Roundtable on Insurance Industry',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'BANGABANDHU\'S HOMECOMING DAY',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Independence & National Day',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Budget 2022',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Important Days',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Jobs and Opportunities',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Golden Jubilee of Independence',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => '100 Years of Sheikh Mujibur Rahman',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Asia Pacific Conference on Financial Inclusive and Sustainable Development',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Environment',
                'children' => []
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Youth and Entrepreneurship',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Youth',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Entrepreneurship',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Startups',
                    ]
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Personal Finance',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Tax',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Borrow',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Mutual Funds',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Save',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'News',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Spend',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Calculators',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Invest',
                    ],
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Lifestyle',
                'children' => [
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Living',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Entertainment',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Food',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Culture',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Others',
                    ],
                    [
                        'status' => 1,
                        'ordering' => 0,
                        'name' => 'Gallery',
                    ]
                ]
            ],
            [
                'status' => 1,
                'ordering' => 0,
                'name' => 'Bangla',
                'children' => []
            ],
        ];

        foreach ($collections as $item) {
            $category = new AssetCategory();
            $category->fill([
                'status' => $item['status'],
                'ordering' => $item['ordering'],
                'name' => $item['name'],
            ]);
            $category->save();
            if (count($item['children']) > 0) {
                $category->children()->createMany($item['children']);
            }
        }
    }
}