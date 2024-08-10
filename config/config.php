<?php

return [
    /*
     * Application front end url.
     */
    'url' => env('FRONTEND_URL', 'http://localhost:3000'),

    /*
    * Application admin panel url.
    */
    'admin_url' => env('ADMIN_URL', 'http://localhost:3000'),

    /*
    * Contact mail to address.
    */
    'mail_to_address' => env('MAIL_TO_ADDRESS', 'eliyas.batterylowinteractive@gmail.com'),

    /*
     * User email verification url.
     */
    'email_verify_url' => env('ADMIN_EMAIL_VERIFY_URL', '/auth/email-verify?queryURL='),

    /*
     * User email verification url.
     */
    'reset_password_url' => env('ADMIN_RESET_PASSWORD_URL', '/auth/reset-password/'),

    /*
     * Category id from database.
     */
    'category' => [
        'economy' => env('CATEGORY_ECONOMY'),
        'economy_bangladesh' => env('CATEGORY_ECONOMY_BANGLADESH'),
        'economy_global' => env('CATEGORY_ECONOMY_GLOBAL'),
        'stock' => env('CATEGORY_STOCK'),
        'stock_bangladesh' => env('CATEGORY_STOCK_BANGLADESH'),
        'stock_global' => env('CATEGORY_STOCK_GLOBAL'),
        'trade' => env('CATEGORY_TRADE'),
        'national' => env('CATEGORY_NATIONAL'),
        'national_all' => env('CATEGORY_NATIONAL_ALL'),
        'national_politics' => env('CATEGORY_NATIONAL_POLITICS'),
        'national_country' => env('CATEGORY_NATIONAL_COUNTRY'),
        'national_crime' => env('CATEGORY_NATIONAL_CRIME'),
        'sports_parent' => env('CATEGORY_SPORTS_PARENT'),
        'sports' => env('CATEGORY_SPORTS'),
        'sports_cricket' => env('CATEGORY_SPORTS_CRICKET'),
        'sports_football' => env('CATEGORY_SPORTS_FOOTBALL'),
        'sports_more_sports' => env('CATEGORY_SPORTS_MORE_SPORTS'),
        'sports_category_slider' => env('CATEGORY_SPORTS_SLIDER'),   
        'lifestyle' => env('CATEGORY_LIFESTYLE'), 
        'lifestyle_entertainment' => env('CATEGORY_LIFESTYLE_ENTERTAINMENT'),        
        'lifestyle_living' => env('CATEGORY_LIFESTYLE_LIVING'),
        'lifestyle_food' => env('CATEGORY_LIFESTYLE_FOOD'),
        'lifestyle_gallery' => env('CATEGORY_LIFESTYLE_GALLERY'),
        'lifestyle_culture' => env('CATEGORY_LIFESTYLE_CULTURE'),
        'lifestyle_others' => env('CATEGORY_LIFESTYLE_OTHERS'),
        'world' => env('CATEGORY_WORLD'),
        'world_asia' => env('CATEGORY_WORLD_ASIA'),
        'world_america' => env('CATEGORY_WORLD_AMERICA'),
        'world_europe' => env('CATEGORY_WORLD_EUROPE'),
        'world_africa' => env('CATEGORY_WORLD_AFRICA'),
        'education' => env('CATEGORY_EDUCATION'),
        'education_article' => env('CATEGORY_EDUCATION_ARTICLE'),
        'scitech' => env('CATEGORY_SCITECH'),
        'health' => env('CATEGORY_HEALTH'),
        'entertainment' => env('CATEGORY_ENTERTAINMENT'),
        'environment' => env('CATEGORY_ENVIRONMENT'),
        'jobs_and_opportunities' => env('CATEGORY_JOBS_AND_OPPORTUNITIES'),
        'views' => env('CATEGORY_VIEWS'),
        'views_views' => env('CATEGORY_VIEWS_VIEWS'),
        'views_reviews' => env('CATEGORY_VIEWS_REVIEWS'),
        'views_opinions' => env('CATEGORY_VIEWS_OPINIONS'),
        'views_columns' => env('CATEGORY_VIEWS_COLUMNS'),
        'views_analysis' => env('CATEGORY_VIEWS_ANALYSIS'),
        'views_letters' => env('CATEGORY_VIEWS_LETTERS'),
        'views_economictrends' => env('CATEGORY_VIEWS_ECONOMICTRENDS'),
        'views_all' => env('CATEGORY_VIEWS_ALL'),
        'more' => env('CATEGORY_MORE'),
        'golden_jubilee_of_independence' => env('CATEGORY_GOLDENJUBILEEOFINDEPENDENCE'),
        'youth_and_entrepreneurship' => env('CATEGORY_YOUTH_AND_ENTREPRENEURSHIP'),
        'youth_and_entrepreneurship_youth' => env('CATEGORY_YOUTH_AND_ENTREPRENEURSHIP_YOUTH'),
        'youth_and_entrepreneurship_entrepreneurship' => env('CATEGORY_YOUTH_AND_ENTREPRENEURSHIP_ENTREPRENEURSHIP'),
        'youth_and_entrepreneurship_startups' => env('CATEGORY_YOUTH_AND_ENTREPRENEURSHIP_STARTUPS'),
        'personal_finance' => env('CATEGORY_PERSONAL_FINANCE'),
        'personal_finance_tax' => env('CATEGORY_PERSONAL_FINANCE_TAX'),
        'personal_finance_mutual_funds' => env('CATEGORY_PERSONAL_FINANCE_MUTUAL_FUNDS'),
        'personal_finance_invest' => env('CATEGORY_PERSONAL_FINANCE_INVEST'),
        'personal_finance_save' => env('CATEGORY_PERSONAL_FINANCE_SAVE'),
        'personal_finance_news' => env('CATEGORY_PERSONAL_FINANCE_NEWS'),
        'personal_finance_spend' => env('CATEGORY_PERSONAL_FINANCE_SPEND'),
        'personal_finance_calculators' => env('CATEGORY_PERSONAL_FINANCE_CALCULATORS'),
        'special_issues' => env('CATEGORY_SPECIAL_ISSUES'),
        'special_issues_budget2022' => env('CATEGORY_SPECIAL_ISSUES_BUDGET2022'),
        'special_issues_important_days' => env('CATEGORY_SPECIAL_ISSUES_IMPORTANT_DAYS'),
        'editorial' => env('CATEGORY_EDITORIAL'),
        'bangla' => env('CATEGORY_BANGLA'),
        'others' => env('CATEGORY_OTHERS'),        
    ],

    /*
    * get supported artisan commands.
    */
    'artisan_commands' => [
        'route:cache' => [
            'text' => 'Create a route cache file for faster route registration.',
            'class' => 'primary'
        ],
        'config:cache' => [
            'text' => 'Create a cache file for faster configuration loading.',
            'class' => 'primary'
        ],
        'optimize' => [
            'text' => 'Cache the framework bootstrap files.',
            'class' => 'primary'
        ],
        'view:cache' => [
            'text' => 'Compile all of the application\'s Blade templates.',
            'class' => 'primary'
        ],
        'storage:link' => [
            'text' => 'Create the symbolic links configured for the application.',
            'class' => 'primary'
        ],
        'route:clear' => [
            'text' => 'Remove the route cache file.',
            'class' => 'warning'
        ],
        'config:clear' => [
            'text' => 'Remove the configuration cache file.',
            'class' => 'warning'
        ],
        'cache:clear' => [
            'text' => 'Flush the application cache.',
            'class' => 'warning'
        ],
        'view:clear' => [
            'text' => 'Clear all compiled view files.',
            'class' => 'warning'
        ],
        'permission:cache-reset' => [
            'text' => 'Reset the permission cache.',
            'class' => 'warning'
        ],
        'auth:clear-resets' => [
            'text' => 'Flush expired password reset tokens.',
            'class' => 'warning'
        ],
        'medialibrary:clean' => [
            'text' => 'Clean deprecated conversions and files without related model.',
            'class' => 'warning'
        ],
        'optimize:clear' => [
            'text' => 'Remove the cached bootstrap files.',
            'class' => 'warning'
        ],
        'clear-compiled' => [
            'text' => 'Remove the compiled class file.',
            'class' => 'warning'
        ]
    ]
];