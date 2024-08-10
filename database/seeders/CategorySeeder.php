<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
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
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Economy',
                    'slug' => 'economy'
                ],
                'bn' => [
                    'name' => 'অর্থনীতি',
                    'slug' => 'economy'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Bangladesh',
                            'slug' => 'economy/bangladesh'
                        ],
                        'bn' => [
                            'name' => 'বাংলাদেশ',
                            'slug' => 'economy/bangladesh'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Global',
                            'slug' => 'economy/global'
                        ],
                        'bn' => [
                            'name' => 'বিশ্বব্যাপী',
                            'slug' => 'economy/global'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Budget-2019-20',
                            'slug' => 'economy/budget-2019-20'
                        ],
                        'bn' => [
                            'name' => 'বাজেট-2019-20',
                            'slug' => 'economy/budget-2019-20'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Budget-2020-21',
                            'slug' => 'economy/budget-2020-21'
                        ],
                        'bn' => [
                            'name' => 'বাজেট-2020-21',
                            'slug' => 'economy/budget-2020-21'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Stock',
                    'slug' => 'stock'
                ],
                'bn' => [
                    'name' => 'স্টক',
                    'slug' => 'stock'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Bangladesh',
                            'slug' => 'stock/bangladesh'
                        ],
                        'bn' => [
                            'name' => 'বাংলাদেশ',
                            'slug' => 'stock/bangladesh'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Global',
                            'slug' => 'stock/global'
                        ],
                        'bn' => [
                            'name' => 'বিশ্বব্যাপী',
                            'slug' => 'stock/global'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Trade',
                    'slug' => 'trade'
                ],
                'bn' => [
                    'name' => 'বাণিজ্য',
                    'slug' => 'trade'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'National',
                    'slug' => 'national'
                ],
                'bn' => [
                    'name' => 'জাতীয়',
                    'slug' => 'national'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Politics',
                            'slug' => 'national/politics'
                        ],
                        'bn' => [
                            'name' => 'রাজনীতি',
                            'slug' => 'national/politics'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Country',
                            'slug' => 'national/country'
                        ],
                        'bn' => [
                            'name' => 'দেশ',
                            'slug' => 'national/country'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Law & Order',
                            'slug' => 'national/crime'
                        ],
                        'bn' => [
                            'name' => 'অপরাধ',
                            'slug' => 'national/crime'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'World',
                    'slug' => 'world'
                ],
                'bn' => [
                    'name' => 'বিশ্ব',
                    'slug' => 'world'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'America',
                            'slug' => 'world/america'
                        ],
                        'bn' => [
                            'name' => 'আমেরিকা',
                            'slug' => 'world/america'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Asia/South Asia',
                            'slug' => 'world/asia'
                        ],
                        'bn' => [
                            'name' => 'এশিয়া/দক্ষিণ এশিয়া',
                            'slug' => 'world/asia'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Europe',
                            'slug' => 'world/europe'
                        ],
                        'bn' => [
                            'name' => 'ইউরোপ',
                            'slug' => 'world/europe'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Africa',
                            'slug' => 'world/africa'
                        ],
                        'bn' => [
                            'name' => 'আফ্রিকা',
                            'slug' => 'world/africa'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Views',
                    'slug' => 'views'
                ],
                'bn' => [
                    'name' => 'ভিউ',
                    'slug' => 'views'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Views',
                            'slug' => 'views/views'
                        ],
                        'bn' => [
                            'name' => 'ভিউ',
                            'slug' => 'views/views'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Reviews',
                            'slug' => 'views/reviews'
                        ],
                        'bn' => [
                            'name' => 'রিভিউ',
                            'slug' => 'views/reviews'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Opinions',
                            'slug' => 'views/opinions'
                        ],
                        'bn' => [
                            'name' => 'মতামত',
                            'slug' => 'views/opinions'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Columns',
                            'slug' => 'views/columns'
                        ],
                        'bn' => [
                            'name' => 'কলাম',
                            'slug' => 'views/columns'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Analysis',
                            'slug' => 'views/analysis'
                        ],
                        'bn' => [
                            'name' => 'বিশ্লেষণ',
                            'slug' => 'views/analysis'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Letters',
                            'slug' => 'views/letters'
                        ],
                        'bn' => [
                            'name' => 'অক্ষর',
                            'slug' => 'views/letters'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Economic Trends and Insights',
                            'slug' => 'views/economic-trends-and-insights'
                        ],
                        'bn' => [
                            'name' => 'অর্থনৈতিক প্রবণতা এবং অন্তর্দৃষ্টি',
                            'slug' => 'views/economic-trends-and-insights'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Editorial',
                    'slug' => 'editorial'
                ],
                'bn' => [
                    'name' => 'সম্পাদকীয়',
                    'slug' => 'editorial'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Education',
                    'slug' => 'education'
                ],
                'bn' => [
                    'name' => 'শিক্ষা',
                    'slug' => 'education'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Article',
                            'slug' => 'education/article'
                        ],
                        'bn' => [
                            'name' => 'প্রবন্ধ',
                            'slug' => 'education/article'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Media',
                    'slug' => 'media'
                ],
                'bn' => [
                    'name' => 'মিডিয়া',
                    'slug' => 'media'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Sci-Tech',
                    'slug' => 'sci-tech'
                ],
                'bn' => [
                    'name' => 'বিজ্ঞান-প্রযুক্তি',
                    'slug' => 'sci-tech'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Health',
                    'slug' => 'health'
                ],
                'bn' => [
                    'name' => 'স্বাস্থ্য',
                    'slug' => 'health'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Sports',
                    'slug' => 'sports'
                ],
                'bn' => [
                    'name' => 'খেলাধুলা',
                    'slug' => 'sports'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'World Cup 2019',
                            'slug' => 'sports/world-cup-2019'
                        ],
                        'bn' => [
                            'name' => 'বিশ্বকাপ 2019',
                            'slug' => 'sports/world-cup-2019'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'T20 World Cup 2021',
                            'slug' => 'sports/t20-world-cup-2021'
                        ],
                        'bn' => [
                            'name' => 'টি-টোয়েন্টি বিশ্বকাপ 2021',
                            'slug' => 'sports/t20-world-cup-2021'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Cricket',
                            'slug' => 'sports/cricket'
                        ],
                        'bn' => [
                            'name' => 'ক্রিকেট',
                            'slug' => 'sports/cricket'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Football',
                            'slug' => 'sports/football'
                        ],
                        'bn' => [
                            'name' => 'ফুটবল',
                            'slug' => 'sports/football'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'More Sports',
                            'slug' => 'sports/more-sports'
                        ],
                        'bn' => [
                            'name' => 'অন্যান্য খেলাধুলা',
                            'slug' => 'sports/more-sports'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Entertainment',
                    'slug' => 'entertainment'
                ],
                'bn' => [
                    'name' => 'বিনোদন',
                    'slug' => 'entertainment'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Others',
                    'slug' => 'others'
                ],
                'bn' => [
                    'name' => 'অন্যান্য',
                    'slug' => 'others'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Special Issues',
                    'slug' => 'special-issues'
                ],
                'bn' => [
                    'name' => 'বিশেষ ইস্যু',
                    'slug' => 'special-issues'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Power & Energy',
                            'slug' => 'special-issues/power-energy'
                        ],
                        'bn' => [
                            'name' => 'ক্ষমতা ও শক্তি',
                            'slug' => 'special-issues/power-energy'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'RMG & Textile',
                            'slug' => 'special-issues/rmg-textile'
                        ],
                        'bn' => [
                            'name' => 'আরএমজি এবং টেক্সটাইল',
                            'slug' => 'special-issues/rmg-textile'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Power & Energy 2',
                            'slug' => 'special-issues/power-energy-2'
                        ],
                        'bn' => [
                            'name' => 'ক্ষমতা ও শক্তি 2',
                            'slug' => 'special-issues/power-energy-2'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'RMG & Textile 4',
                            'slug' => 'special-issues/rmg-textile-4'
                        ],
                        'bn' => [
                            'name' => 'আরএমজি এবং টেক্সটাইল 4',
                            'slug' => 'special-issues/rmg-textile-4'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'RMG & Textile 5',
                            'slug' => 'special-issues/rmg-textile-5'
                        ],
                        'bn' => [
                            'name' => 'আরএমজি এবং টেক্সটাইল 5',
                            'slug' => 'special-issues/rmg-textile-5'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Amar Ekushey Special 2018',
                            'slug' => 'special-issues/amar-ekushey-special-2018'
                        ],
                        'bn' => [
                            'name' => 'অমর একুশের স্পেশাল 2018',
                            'slug' => 'special-issues/amar-ekushey-special-2018'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Special on Independence Day',
                            'slug' => 'special-issues/special-on-independence-day'
                        ],
                        'bn' => [
                            'name' => 'স্বাধীনতা দিবসে বিশেষ',
                            'slug' => 'special-issues/special-on-independence-day'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Pahela Baishakh 1425',
                            'slug' => 'special-issues/pahela-baishakh-1425'
                        ],
                        'bn' => [
                            'name' => 'পহেলা বৈশাখ 1425',
                            'slug' => 'special-issues/pahela-baishakh-1425'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'special on National Mourning Day 2018',
                            'slug' => 'special-issues/special-on-national-mourning-day-2018'
                        ],
                        'bn' => [
                            'name' => 'জাতীয় শোক দিবসে বিশেষ 2018',
                            'slug' => 'special-issues/special-on-national-mourning-day-2018'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Special on National Mourning Day',
                            'slug' => 'special-issues/special-on-national-mourning-day'
                        ],
                        'bn' => [
                            'name' => 'জাতীয় শোক দিবসে বিশেষ',
                            'slug' => 'special-issues/special-on-national-mourning-day'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Remembering Moazzem Hossain',
                            'slug' => 'special-issues/remembering-moazzem-hossain'
                        ],
                        'bn' => [
                            'name' => 'মোয়াজ্জেম হোসেনের স্মৃতিতে',
                            'slug' => 'special-issues/remembering-moazzem-hossain'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'PABL-FE Roundtable on Insurance Industry\'s Expectations and Attainments in the Budget 2019-2020',
                            'slug' => 'special-issues/pabl-fe-roundtable-on-insurance-industrys-expectations-and-attainments-in-the-budget-2019-2020'
                        ],
                        'bn' => [
                            'name' => '2019-2020 বাজেটে বীমা শিল্পের প্রত্যাশা এবং অর্জনের উপর PABL-FE গোলটেবিল',
                            'slug' => 'special-issues/pabl-fe-roundtable-on-insurance-industrys-expectations-and-attainments-in-the-budget-2019-2020'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'PABL-FE Roundtable on Insurance Industry',
                            'slug' => 'special-issues/pabl-fe-roundtable-on-insurance-industry'
                        ],
                        'bn' => [
                            'name' => 'বীমা শিল্পের উপর PABL-FE গোলটেবিল',
                            'slug' => 'special-issues/pabl-fe-roundtable-on-insurance-industry'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'BANGABANDHU\'S HOMECOMING DAY',
                            'slug' => 'special-issues/bangabandhus-homecoming-day'
                        ],
                        'bn' => [
                            'name' => 'বঙ্গবন্ধুর স্বদেশ প্রত্যাবর্তন দিবস',
                            'slug' => 'special-issues/bangabandhus-homecoming-day'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Independence & National Day',
                            'slug' => 'special-issues/independence-national-day'
                        ],
                        'bn' => [
                            'name' => 'স্বাধীনতা ও জাতীয় দিবস',
                            'slug' => 'special-issues/independence-national-day'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Budget 2022',
                            'slug' => 'special-issues/budget-2022'
                        ],
                        'bn' => [
                            'name' => 'বাজেট ২০২২',
                            'slug' => 'special-issues/budget-2022'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Important Days',
                            'slug' => 'special-issues/important-days'
                        ],
                        'bn' => [
                            'name' => 'গুরুত্বপূর্ণ দিন',
                            'slug' => 'special-issues/important-days'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Jobs and Opportunities',
                    'slug' => 'jobs-and-opportunities'
                ],
                'bn' => [
                    'name' => 'চাকরি এবং সুযোগ',
                    'slug' => 'jobs-and-opportunities'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Golden Jubilee of Independence',
                    'slug' => 'golden-jubilee-of-independence'
                ],
                'bn' => [
                    'name' => 'স্বাধীনতার সুবর্ণ জয়ন্তী',
                    'slug' => 'golden-jubilee-of-independence'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => '100 Years of Sheikh Mujibur Rahman',
                    'slug' => 'mujib100'
                ],
                'bn' => [
                    'name' => 'শেখ মুজিবুর রহমানের শতবর্ষ',
                    'slug' => 'mujib100'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Asia Pacific Conference on Financial Inclusive and Sustainable Development',
                    'slug' => 'asia-pacific-conference-on-financial-inclusive-and-sustainable-development'
                ],
                'bn' => [
                    'name' => 'আর্থিক অন্তর্ভুক্তিমূলক এবং টেকসই উন্নয়ন বিষয়ে এশিয়া প্যাসিফিক সম্মেলন',
                    'slug' => 'asia-pacific-conference-on-financial-inclusive-and-sustainable-development'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Environment',
                    'slug' => 'environment'
                ],
                'bn' => [
                    'name' => 'পরিবেশ',
                    'slug' => 'environment'
                ],
                'children' => []
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Youth and Entrepreneurship',
                    'slug' => 'youth-and-entrepreneurship'
                ],
                'bn' => [
                    'name' => 'যুব ও উদ্যোক্তা',
                    'slug' => 'youth-and-entrepreneurship'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Youth',
                            'slug' => 'youth-and-entrepreneurship/youth'
                        ],
                        'bn' => [
                            'name' => 'তারুণ্য',
                            'slug' => 'youth-and-entrepreneurship/youth'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Entrepreneurship',
                            'slug' => 'youth-and-entrepreneurship/entrepreneurship'
                        ],
                        'bn' => [
                            'name' => 'শিল্পোদ্যোগ',
                            'slug' => 'youth-and-entrepreneurship/entrepreneurship'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Startups',
                            'slug' => 'youth-and-entrepreneurship/startups'
                        ],
                        'bn' => [
                            'name' => 'স্টার্টআপ',
                            'slug' => 'youth-and-entrepreneurship/startups'
                        ],
                    ]
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Personal Finance',
                    'slug' => 'personal-finance'
                ],
                'bn' => [
                    'name' => 'ব্যক্তিগত অর্থায়ন',
                    'slug' => 'personal-finance'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Tax',
                            'slug' => 'personal-finance/tax'
                        ],
                        'bn' => [
                            'name' => 'ট্যাক্স',
                            'slug' => 'personal-finance/tax'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Borrow',
                            'slug' => 'personal-finance/borrow'
                        ],
                        'bn' => [
                            'name' => 'ধার',
                            'slug' => 'personal-finance/borrow'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Mutual Funds',
                            'slug' => 'personal-finance/mutual-funds'
                        ],
                        'bn' => [
                            'name' => 'একত্রিত পুঁজি',
                            'slug' => 'personal-finance/mutual-funds'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Save',
                            'slug' => 'personal-finance/save'
                        ],
                        'bn' => [
                            'name' => 'সংরক্ষণ',
                            'slug' => 'personal-finance/save'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'News',
                            'slug' => 'personal-finance/news'
                        ],
                        'bn' => [
                            'name' => 'খবর',
                            'slug' => 'personal-finance/news'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Spend',
                            'slug' => 'personal-finance/spend'
                        ],
                        'bn' => [
                            'name' => 'ব্যয়',
                            'slug' => 'personal-finance/spend'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Calculators',
                            'slug' => 'personal-finance/calculators'
                        ],
                        'bn' => [
                            'name' => 'ক্যালকুলেটর',
                            'slug' => 'personal-finance/calculators'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Invest',
                            'slug' => 'personal-finance/invest'
                        ],
                        'bn' => [
                            'name' => 'বিনিয়োগ',
                            'slug' => 'personal-finance/invest'
                        ],
                    ],
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Lifestyle',
                    'slug' => 'lifestyle'
                ],
                'bn' => [
                    'name' => 'লাইফস্টাইল',
                    'slug' => 'lifestyle'
                ],
                'children' => [
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Living',
                            'slug' => 'lifestyle/living'
                        ],
                        'bn' => [
                            'name' => 'সাম্প্রতিক',
                            'slug' => 'lifestyle/living'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Entertainment',
                            'slug' => 'lifestyle/entertainment'
                        ],
                        'bn' => [
                            'name' => 'বিনোদন',
                            'slug' => 'lifestyle/entertainment'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Food',
                            'slug' => 'lifestyle/food'
                        ],
                        'bn' => [
                            'name' => 'খাদ্য',
                            'slug' => 'lifestyle/food'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Culture',
                            'slug' => 'lifestyle/culture'
                        ],
                        'bn' => [
                            'name' => 'সংস্কৃতি',
                            'slug' => 'lifestyle/culture'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Others',
                            'slug' => 'lifestyle/others'
                        ],
                        'bn' => [
                            'name' => 'অন্যান্য',
                            'slug' => 'lifestyle/others'
                        ],
                    ],
                    [
                        'status' => 1,
                        'color' => '#e11d48',
                        'ordering' => 0,
                        'en' => [
                            'name' => 'Gallery',
                            'slug' => 'lifestyle/gallery'
                        ],
                        'bn' => [
                            'name' => 'গ্যালারি',
                            'slug' => 'lifestyle/gallery'
                        ],
                    ]
                ]
            ],
            [
                'status' => 1,
                'color' => '#e11d48',
                'ordering' => 0,
                'en' => [
                    'name' => 'Bangla',
                    'slug' => 'bn'
                ],
                'bn' => [
                    'name' => 'বাংলা',
                    'slug' => 'bn'
                ],
                'children' => []
            ],
        ];

        foreach ($collections as $item) {
            $category = new Category();
            $category->fill([
                'status' => $item['status'],
                'color' => $item['color'],
                'ordering' => $item['ordering'],
                'en' => $item['en'],
                'bn' => $item['bn']
            ]);
            $category->save();
            if (count($item['children']) > 0) {
                $category->children()->createMany($item['children']);
            }
        }
    }
}