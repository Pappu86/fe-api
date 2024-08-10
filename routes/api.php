<?php

use App\Http\Controllers\Api\AdvertisementApiController;
use App\Http\Controllers\Api\Category\EconomyApiController;
use App\Http\Controllers\Api\Category\StockApiController;
use App\Http\Controllers\Api\Category\TradeApiController;
use App\Http\Controllers\Api\Category\NationalApiController;
use App\Http\Controllers\Api\Category\WorldApiController;
use App\Http\Controllers\Api\Category\ViewsApiController;
use App\Http\Controllers\Api\Category\EducationApiController;
use App\Http\Controllers\Api\Category\SciTechApiController;
use App\Http\Controllers\Api\Category\HealthApiController;
use App\Http\Controllers\Api\Category\SportsApiController;
use App\Http\Controllers\Api\Category\LifestyleApiController;
use App\Http\Controllers\Api\Category\EntertainmentApiController;
use App\Http\Controllers\Api\Category\EnvironmentApiController;
use App\Http\Controllers\Api\Category\JobsAndOpportunitiesApiController;
use App\Http\Controllers\Api\Category\GoldenJubileeApiController;
use App\Http\Controllers\Api\Category\YouthApiController;
use App\Http\Controllers\Api\Category\PersonalFinanceApiController;
use App\Http\Controllers\Api\Category\SpecialIssuesApiController;
use App\Http\Controllers\Api\Category\EditorialApiController;
use App\Http\Controllers\Api\Category\BanglaApiController;
use App\Http\Controllers\Api\Category\OthersApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\FilterSearchApiController;
use App\Http\Controllers\Api\LatestPostApiController;
use App\Http\Controllers\Api\LiveMediaApiController;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\PostSearchApiController;
use App\Http\Controllers\Api\SliderApiController;
use App\Http\Controllers\Api\ReporterApiController;
use App\Http\Controllers\Api\ApiDataProviderController;
use App\Http\Controllers\Api\NewsletterApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// locales
Route::prefix('{locale}')->group(function () {
    Route::get('slider', [SliderApiController::class, 'index']);
    // latest post
    Route::get('latest-post', [LatestPostApiController::class, 'index']);
    // reporter
    Route::prefix('reporter')->group(function () {
        Route::get('{username}', [ReporterApiController::class, 'show']);
        Route::get('{username}/more', [ReporterApiController::class, 'getReporterMorePosts']);
    });
    // search filters
    Route::prefix('filter')->group(function () {
        Route::get('reporters', [FilterSearchApiController::class, 'getReporters']);
        Route::get('categories', [FilterSearchApiController::class, 'getCategories']);
    });
    // post search
    Route::get('post-search', [PostSearchApiController::class, 'search']);
    // Route::get('post-search-result', [PostSearchApiController::class, 'getSearchResult']);
    
    // post route
    Route::get('post/{slug}', [PostApiController::class, 'show']);
    // post more route
    Route::get('post-more/{slug}', [PostApiController::class, 'postMore']);
    // post most read route
    Route::get('post-most-read', [PostApiController::class, 'postMostRead']);
    // all posts route
    Route::get('posts', [PostApiController::class, 'allPosts']);

    // It's temporary remove it after updated
    // Route::get('posts/content-update', [PostApiController::class, 'updatePostContent']);
    // Route::post('posts/content-update', [PostApiController::class, 'updatePostContent']);
    // Route::get('posts/search-missing-posts-import', [PostApiController::class, 'searchMissingPostsImport']);
    // Route::get('posts/update-post-property', [PostApiController::class, 'updatePostProperty']);
    // Route::get('posts/remove-search-index', [PostApiController::class, 'removeSearchIndexFromMailisearch']);

    Route::prefix('home')->group(function () {
        // post routes
        Route::get('post/{type}', [PostApiController::class, 'index']);
        // economy category
        Route::get('category-economy', [PostApiController::class, 'getCategoryEconomyPosts']);
        // category most read post routes
        Route::get('most-read', [PostApiController::class, 'getMostReadPosts']);
        // category stock post routes
        Route::get('category-stock', [PostApiController::class, 'getCategoryStockPosts']);
        // category trade post routes
        Route::get('category-trade', [PostApiController::class, 'getCategoryTradePosts']);
        // category national post routes
        Route::get('category-national', [PostApiController::class, 'getCategoryNationalPosts']);
        // category sports post routes
        Route::get('category-sports', [PostApiController::class, 'getCategorySportsPosts']);
        // category education post routes
        Route::get('category-education', [PostApiController::class, 'getCategoryEducationPosts']);
        // category lifestyle post routes
        Route::get('category-lifestyle', [PostApiController::class, 'getCategoryLifestylePosts']);
        // category world post routes
        Route::get('category-world', [PostApiController::class, 'getCategoryWorldPosts']);
        // category more post routes
        Route::get('category-more', [PostApiController::class, 'getCategoryMore']);
        // category youth and entrepreneurship post routes
        Route::get('category-youth-and-entrepreneurship', [PostApiController::class, 'getCategoryYouthPosts']);
        // category youth and entrepreneurship post routes
        Route::get('category-personal-finance', [PostApiController::class, 'getCategoryPersonalFinancePosts']);
        // category trade post routes
        Route::get('category-bangla', [PostApiController::class, 'getCategoryBanglaPosts']);
        // category views post routes
        Route::get('category-views', [PostApiController::class, 'getCategoryViewsPosts']);
    });

    // category related routes
    Route::prefix('category')->group(function () {
        Route::prefix('economy')->group(function () {
            Route::get('home', [EconomyApiController::class, 'getEconomyPosts']);
            Route::get('bangladesh', [EconomyApiController::class, 'getEconomyBangladeshPosts']);
            Route::get('global', [EconomyApiController::class, 'getEconomyGlobalPosts']);
            Route::get('more', [EconomyApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('stock')->group(function () {
            Route::get('home', [StockApiController::class, 'getStockPosts']);
            Route::get('bangladesh', [StockApiController::class, 'getStockBangladeshPosts']);
            Route::get('global', [StockApiController::class, 'getStockGlobalPosts']);
            Route::get('more', [StockApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('trade')->group(function () {
            Route::get('home', [TradeApiController::class, 'getTradePosts']);
            Route::get('more', [TradeApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('national')->group(function () {
            Route::get('home', [NationalApiController::class, 'getNationalPosts']);
            Route::get('politics', [NationalApiController::class, 'getNationalPoliticsPosts']);
            Route::get('country', [NationalApiController::class, 'getNationalCountryPosts']);
            Route::get('crime', [NationalApiController::class, 'getNationalCrimePosts']);
            Route::get('more', [NationalApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('world')->group(function () {
            Route::get('home', [WorldApiController::class, 'getWorldPosts']);
            Route::get('asia', [WorldApiController::class, 'getWorldAsiaPosts']);
            Route::get('america', [WorldApiController::class, 'getWorldAmericaPosts']);
            Route::get('europe', [WorldApiController::class, 'getWorldEuropePosts']);
            Route::get('africa', [WorldApiController::class, 'getWorldAfricaPosts']);
            Route::get('more', [WorldApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('views')->group(function () {
            Route::get('home', [ViewsApiController::class, 'getViewsPosts']);
            Route::get('views', [ViewsApiController::class, 'getViewsViewsPosts']);
            Route::get('reviews', [ViewsApiController::class, 'getViewsReviewsPosts']);
            Route::get('opinions', [ViewsApiController::class, 'getViewsOpinionsPosts']);
            Route::get('columns', [ViewsApiController::class, 'getViewsColumnsPosts']);
            Route::get('analysis', [ViewsApiController::class, 'getViewsAnalysisPosts']);
            Route::get('letters', [ViewsApiController::class, 'getViewsLettersPosts']);
            Route::get('economic-trends-and-insights', [ViewsApiController::class, 'getViewsEconomicTrendsPosts']);
            Route::get('more', [ViewsApiController::class, 'getCategoryMorePosts']);
            Route::get('sub-more/{category}/{category_id}', [ViewsApiController::class, 'getSubCategoryMorePosts']);
        });
        Route::prefix('education')->group(function () {
            Route::get('home', [EducationApiController::class, 'getEducationPosts']);
            Route::get('article', [EducationApiController::class, 'getEducationArticlePosts']);
            Route::get('more', [EducationApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('sci-tech')->group(function () {
            Route::get('home', [SciTechApiController::class, 'getSciTechPosts']);
            Route::get('more', [SciTechApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('health')->group(function () {
            Route::get('home', [HealthApiController::class, 'getHealthPosts']);
            Route::get('more', [HealthApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('sports')->group(function () {
            Route::get('home', [SportsApiController::class, 'getSportsPosts']);
            Route::get('cricket', [SportsApiController::class, 'getCricketPosts']);
            Route::get('football', [SportsApiController::class, 'getFootballPosts']);
            Route::get('more', [SportsApiController::class, 'getCategoryMorePosts']);
            Route::get('slider', [SportsApiController::class, 'getSportsSliderImages']);
        });
        Route::prefix('lifestyle')->group(function () {
            Route::get('home', [LifestyleApiController::class, 'getLifeStylePosts']);
            Route::get('entertainment', [LifestyleApiController::class, 'getEntertainmentPosts']);
            Route::get('living', [LifestyleApiController::class, 'getLivingPosts']);
            Route::get('food', [LifestyleApiController::class, 'getFoodPosts']);
            Route::get('gallery', [LifestyleApiController::class, 'getGalleryPosts']);
            Route::get('culture', [LifestyleApiController::class, 'getCulturePosts']);
            Route::get('others', [LifestyleApiController::class, 'getOthersPosts']);
            Route::get('more', [LifestyleApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('entertainment')->group(function () {
            Route::get('home', [EntertainmentApiController::class, 'getEntertainmentPosts']);
            Route::get('more', [EntertainmentApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('environment')->group(function () {
            Route::get('home', [EnvironmentApiController::class, 'getEnvironmentPosts']);
            Route::get('more', [EnvironmentApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('jobs-and-opportunities')->group(function () {
            Route::get('home', [JobsAndOpportunitiesApiController::class, 'getJobsAndOpportunitiesPosts']);
            Route::get('more', [JobsAndOpportunitiesApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('golden-jubilee-of-independence')->group(function () {
            Route::get('home', [GoldenJubileeApiController::class, 'getGoldenJubileeOfIndependencePosts']);
            Route::get('more', [GoldenJubileeApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('youth-and-entrepreneurship')->group(function () {
            Route::get('home', [YouthApiController::class, 'getYouthAndEntrepreneurshipPosts']);
            Route::get('youth', [YouthApiController::class, 'getYouthAndEntrepreneurshipYouthPosts']);
            Route::get('entrepreneurship', [YouthApiController::class, 'getYouthAndEntrepreneurshipEntrepreneurshipPosts']);
            Route::get('startups', [YouthApiController::class, 'getYouthAndEntrepreneurshipStartupsPosts']);
            Route::get('more', [YouthApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('personal-finance')->group(function () {
            Route::get('home', [PersonalFinanceApiController::class, 'getPersonalFinancePosts']);
            Route::get('tax', [PersonalFinanceApiController::class, 'getPersonalFinanceTaxPosts']);
            Route::get('mutual-funds', [PersonalFinanceApiController::class, 'getPersonalFinanceMutualFundsPosts']);
            Route::get('invest', [PersonalFinanceApiController::class, 'getPersonalFinanceInvestPosts']);
            Route::get('save', [PersonalFinanceApiController::class, 'getPersonalFinanceSavePosts']);
            Route::get('news', [PersonalFinanceApiController::class, 'getPersonalFinanceNewsPosts']);
            Route::get('spend', [PersonalFinanceApiController::class, 'getPersonalFinanceSpendPosts']);
            Route::get('calculators', [PersonalFinanceApiController::class, 'getPersonalFinanceCalculatorsPosts']);
            Route::get('more', [PersonalFinanceApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('special-issues')->group(function () {
            Route::get('home', [SpecialIssuesApiController::class, 'getSpecialIssuesPosts']);
            Route::get('budget-2022', [SpecialIssuesApiController::class, 'getSpecialIssuesBudget2022Posts']);
            Route::get('important-days', [SpecialIssuesApiController::class, 'getSpecialIssuesImportantDaysPosts']);
            Route::get('more', [SpecialIssuesApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('editorial')->group(function () {
            Route::get('home', [EditorialApiController::class, 'getEditorialPosts']);
            Route::get('more', [EditorialApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('bangla')->group(function () {
            Route::get('home', [BanglaApiController::class, 'getBanglaPosts']);
            Route::get('more', [BanglaApiController::class, 'getCategoryMorePosts']);
        });
        Route::prefix('others')->group(function () {
            Route::get('home', [OthersApiController::class, 'getOthersPosts']);
            Route::get('more', [OthersApiController::class, 'getCategoryMorePosts']);
        });
    });

    Route::prefix('subcategory')->group(function () {
        Route::get('home', [CategoryApiController::class, 'getHomePosts']);
        Route::get('more', [CategoryApiController::class, 'getMorePosts']);
    });

    // live media routes
    Route::prefix('live-media')->group(function () {
        Route::get('home', [LiveMediaApiController::class, 'home']);
        Route::get('featured', [LiveMediaApiController::class, 'featured']);
        Route::get('get', [LiveMediaApiController::class, 'index']);
    });

    // For Newslatter
    Route::prefix('newsletter')->group(function () {
        Route::post('/subscribe', [NewsletterApiController::class, 'createSubscriber']);
    });
});

// ads
Route::prefix('revenue')->group(function () {
    Route::get('global', [AdvertisementApiController::class, 'getGlobalAds']);
    Route::get('{page}', [AdvertisementApiController::class, 'getAds']);
});

//For APP
// Route::get('/get-home', [ApiDataProviderController::class, 'getHome']);
// Route::get('/get-category', [ApiDataProviderController::class, 'getPage']);

// Route::prefix('api')->group(function () {
//    Route::get('/get-home', [ApiDataProviderController::class, 'getHome']);
// });