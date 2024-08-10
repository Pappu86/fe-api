<?php

namespace Database\Seeders;

use App\Models\AdsPosition;
use Illuminate\Database\Seeder;

class AdsPositionSeeder extends Seeder
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
                'page' => 'home',
                'section' => 'header',
                'name' => 'header_ad_above_the_logo',
                'size' => '728x90'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'bellow_main_menu',
                'name' => 'ads_1',
                'size' => '367x100'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'bellow_main_menu',
                'name' => 'ads_2',
                'size' => '367x100'
            ],
            [
                'status' => 0,
                'page' => 'home',
                'section' => 'bellow_main_menu',
                'name' => 'ads_3',
                'size' => '367x100'
            ],
            [
                'status' => 0,
                'page' => 'home',
                'section' => 'bellow_main_menu',
                'name' => 'ads_4',
                'size' => '367x100'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'hero_section',
                'name' => 'hero_section_1st_column_bottom',
                'size' => '288x250'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'hero_section',
                'name' => 'under_main_slider',
                'size' => '592x100'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'hero_section',
                'name' => 'hero_section_3rd_column_bottom',
                'size' => '250x250'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'hero_section',
                'name' => 'hero_section_4th_column_bottom',
                'size' => '288x250'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'hero_section',
                'name' => 'between_hero_and_economy_section',
                'size' => '728x90'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'economy',
                'name' => 'under_economy_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'bangla',
                'name' => 'bangla_4th_column_ad_1',
                'size' => '288x328'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'bangla',
                'name' => 'bangla_4th_column_ad_2',
                'size' => '288x328'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'bangla',
                'name' => 'under_bangla_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'most_read',
                'name' => '4th_column_mr',
                'size' => '288x680'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'stock',
                'name' => 'under_stock_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'trade',
                'name' => 'trade_4th_column_ad_1',
                'size' => '288x328'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'trade',
                'name' => 'trade_4th_column_ad_2',
                'size' => '288x328'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'trade',
                'name' => 'under_trade_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'national',
                'name' => 'under_national_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'media',
                'name' => 'under_media_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'sports',
                'name' => 'under_sports_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'education',
                'name' => '4th_column_edu',
                'size' => '288x680'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'education',
                'name' => 'under_education_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'youth_and_entrepreneurship',
                'name' => 'under_youth_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'lifestyle',
                'name' => 'under_lifestyle_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'world',
                'name' => 'world_4th_column_ad_1',
                'size' => '288x492'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'world',
                'name' => 'world_4th_column_ad_2',
                'size' => '288x492'
            ],
            [
                'status' => 1,
                'page' => 'home',
                'section' => 'personal_finance',
                'name' => 'under_personal_finance_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'category_with_child',
                'section' => 'main',
                'name' => '2nd_column_1',
                'size' => '288x346'
            ],
            [
                'status' => 1,
                'page' => 'category_with_child',
                'section' => 'main',
                'name' => 'ad_under_main_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'category_with_child',
                'section' => 'between_child_category',
                'name' => 'ad_between_child_category_section_1',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'category_with_child',
                'section' => 'between_child_category',
                'name' => 'ad_between_child_category_section_2',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'category_with_child',
                'section' => 'between_child_category',
                'name' => 'ad_between_child_category_section_3',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'category_with_child',
                'section' => 'between_child_category',
                'name' => 'ad_between_child_category_section_4',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'category_with_child',
                'section' => 'between_child_category',
                'name' => 'ad_between_child_category_section_5',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'category_with_child',
                'section' => 'more_section',
                'name' => 'above_more_section_1',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '3rd_column',
                'name' => 'above_more_news_section',
                'size' => '294x291'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '2nd_column',
                'name' => 'under_the_post_image',
                'size' => '891x112'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '2nd_column',
                'name' => 'inside_the_article_text_body',
                'size' => '303x250'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '3rd_column',
                'name' => 'ad_between_more_news_and_most_read_news',
                'size' => '289x242'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '1st_column',
                'name' => 'ad_under_social_share',
                'size' => '153x289'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '2nd_column',
                'name' => 'ad_at_the_end_of_the_article',
                'size' => '781x250'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '3rd_column',
                'name' => 'ad_at_the_bottom_of_the_row',
                'size' => '287x574'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '2nd_column',
                'name' => 'ad_at_the_second_last_row_bottom_of_the_page',
                'size' => '1158x588'
            ],
            [
                'status' => 1,
                'page' => 'single_news_page',
                'section' => '2nd_column',
                'name' => 'ad_at_the_last_row_bottom_of_the_page',
                'size' => '1158x368'
            ],
            [
                'status' => 1,
                'page' => 'category_without_child',
                'section' => 'main',
                'name' => '2nd_column_2',
                'size' => '288x346'
            ],
            [
                'status' => 1,
                'page' => 'category_without_child',
                'section' => 'more_section',
                'name' => 'above_more_section_2',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'views',
                'section' => 'main',
                'name' => 'under_views_main_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'views',
                'section' => 'reviews',
                'name' => 'under_review_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'views',
                'section' => 'opinions',
                'name' => 'under_opinion_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'views',
                'section' => 'columns',
                'name' => 'under_column_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'views',
                'section' => 'analysis',
                'name' => 'under_analysis_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'views',
                'section' => 'letters',
                'name' => 'under_letter_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'views',
                'section' => 'economic_trends_and_insights',
                'name' => 'under_economic_trends_and_insights_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'main',
                'name' => 'under_lifestyle_main_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'living',
                'name' => 'under_living_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'entertainment',
                'name' => 'under_entertainment_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'food',
                'name' => 'under_food_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'culture',
                'name' => 'under_culture_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'others',
                'name' => 'under_others_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'gallery',
                'name' => 'under_gallery_section',
                'size' => '1200x280'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'tab_section',
                'name' => 'lifestyle_tab_sec_3rd_column_ad',
                'size' => '288x680'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'entertainment',
                'name' => 'entertainment_2nd_column_ad_1',
                'size' => '288x328'
            ],
            [
                'status' => 1,
                'page' => 'lifestyle',
                'section' => 'entertainment',
                'name' => 'entertainment_2nd_column_ad_2',
                'size' => '288x328'
            ],
            [
                'status' => 1,
                'page' => 'modal_ad',
                'section' => 'center_modal',
                'name' => 'center_modal_ad',
                'size' => '500x300'
            ],
            [
                'status' => 1,
                'page' => 'modal_ad',
                'section' => 'footer_modal',
                'name' => 'footer_modal_ad',
                'size' => '970x100'
            ],
            [
                'status' => 1,
                'page' => 'modal_ad',
                'section' => 'top_modal',
                'name' => 'top_modal_ad',
                'size' => '970x100'
            ],
        ];
        foreach ($collections as $collection) {
            $position = new AdsPosition();
            $position->fill($collection);
            $position->save();
        }
    }
}
