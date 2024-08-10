<?php

namespace App\Traits;

use App\Http\Resources\Api\Category\CategoryEconomyApiResource;
use App\Http\Resources\Api\Category\CategoryTradeApiResource;
use App\Http\Resources\Api\Category\CategoryWorldApiResource;
use App\Http\Resources\Api\Category\CategoryLifestyleApiResource;
use App\Http\Resources\Api\Category\CategoryEducationApiResource;
use App\Http\Resources\Api\Category\CategorySportsApiResource;
use App\Http\Resources\Api\Category\CategoryNationalApiResource;
use App\Http\Resources\Api\Category\CategoryStockApiResource;
use App\Http\Resources\Api\Category\CategoryYouthApiResource;
use App\Http\Resources\Api\Category\CategoryPersonalFinanceApiResource;
use App\Http\Resources\Api\CategoryMoreApiResource;
use App\Http\Resources\Api\PostFeaturedApiResource;
use App\Http\Resources\Api\PostOpEdApiResource;
use App\Http\Resources\Api\PostTitleApiResource;
use App\Http\Resources\Api\PostWithImageApiResource;
use App\Http\Resources\Api\PostWithTextApiResource;
use App\Models\Category;
use App\Models\OpedPost;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

trait CategoryResponse
{
    /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    protected function getCategoryMoreResponse($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $categories = json_decode('[' . config('config.category.more') . ']', true);
        $category_ids = implode(',', $categories);

        $posts = Category::with(['translations', 'posts' => function ($q) use ($locale) {
            $q->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            });
        }])
            ->whereIn('id', $categories)
            ->orderByRaw("FIELD(id, $category_ids)")
            ->get();
        ResourceCollection::withoutWrapping();

        return CategoryMoreApiResource::collection($posts);
    }

    /**
     * @param $locale
     * @return CategoryEconomyApiResource
     */
    protected function getEconomyCategoryResponse($locale): CategoryEconomyApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.economy');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $economyPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(2)
                ->get();
            $category->posts = PostWithImageApiResource::collection($economyPosts);

            // economy bangladesh
            $bangladesh = (int)config('config.category.economy_bangladesh');
            $bangladeshPosts = $this->getPosts($locale)
                ->where('category_id', '=', $bangladesh)
                ->limit(4)
                ->get();
            // economy global
            $global = (int)config('config.category.economy_global');
            $globalPosts = $this->getPosts($locale)
                ->where('category_id', '=', $global)
                ->limit(4)
                ->get();

            $titles = $bangladeshPosts->merge($globalPosts);
            $category->titles = PostTitleApiResource::collection($titles);

            // get op-ed posts
            $post_ids = OpedPost::query()->where('category_id', '=', $categoryId)->pluck('post_id')->toArray();
            $oped = $this->getPosts($locale)->whereIn('id', $post_ids)->limit(2)->get();
            $category->oped = PostOpEdApiResource::collection($oped);
        }

        return CategoryEconomyApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryWorldApiResource
     */
    protected function getWorldCategoryResponse($locale): CategoryWorldApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.world');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $worldPosts = $this->getPosts($locale)
                ->where('id', '!=', $featured?->id)
                ->where('category_id', '=', $categoryId)
                ->limit(2)
                ->get();
            $category->posts = PostWithImageApiResource::collection($worldPosts);

            // world asia
            $asia = (int)config('config.category.world_asia');
            $asiaPosts = $this->getPosts($locale)
                ->where('category_id', '=', $asia)
                ->limit(3)
                ->get();

            $category->asia = PostWithImageApiResource::collection($asiaPosts);

            // world america
            $america = (int)config('config.category.world_america');
            $americaPosts = $this->getPosts($locale)
                ->where('category_id', '=', $america)
                ->limit(2)
                ->get();

            $category->america = PostWithImageApiResource::collection($americaPosts);

            // world europe
            $europe = (int)config('config.category.world_europe');
            $europePosts = $this->getPosts($locale)
                ->where('category_id', '=', $europe)
                ->limit(2)
                ->get();

            $category->europe = PostWithImageApiResource::collection($europePosts);

            // world africa
            $africa = (int)config('config.category.world_africa');
            $africaPosts = $this->getPosts($locale)
                ->where('category_id', '=', $africa)
                ->limit(2)
                ->get();

            $category->africa = PostWithImageApiResource::collection($africaPosts);
        }

        return CategoryWorldApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryLifestyleApiResource
     */
    protected function getLifestyleCategoryResponse($locale): CategoryLifestyleApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.lifestyle');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $lifestylePosts = $this->getPosts($locale)
                ->where('id', '!=', $featured?->id)
                ->where('category_id', '=', $categoryId)
                ->limit(4)
                ->get();
            $category->posts = PostWithImageApiResource::collection($lifestylePosts);

            // Prepare exclude post ids
            $lifestylePostsIds = $lifestylePosts->pluck('id')->toArray();
            $ids = array_merge([$featured?->id], $lifestylePostsIds);

            $lifestyleTitles = $this->getPostsByViewCount($locale)
                ->whereNotIn('id', $ids)
                ->where('category_id', '=', $categoryId)
                ->limit(15)
                ->get();
            $category->titles = PostTitleApiResource::collection($lifestyleTitles);

            // Living
            $livingId = (int)config('config.category.lifestyle_living');
            $livingPost = $this->getPosts($locale)
                ->where('category_id', '=', $livingId)
                ->limit(2)
                ->get();
            $category->living = PostWithImageApiResource::collection($livingPost);

            // Entertainment
            $entertainmentId = (int)config('config.category.lifestyle_entertainment');
            $entertainment = $this->getPosts($locale)
                ->where('category_id', '=', $entertainmentId)
                ->limit(2)
                ->get();
            $category->entertainment = PostWithImageApiResource::collection($entertainment);

            // Food
            $foodId = (int)config('config.category.lifestyle_food');
            $foods = $this->getPosts($locale)
                ->where('category_id', '=', $foodId)
                ->limit(2)
                ->get();
            $category->foods = PostWithImageApiResource::collection($foods);

            // Culture
            $cultureId = (int)config('config.category.lifestyle_culture');
            $culture = $this->getPosts($locale)
                ->where('category_id', '=', $cultureId)
                ->limit(2)
                ->get();
            $category->culture = PostWithImageApiResource::collection($culture);

            // Others
            $othersId = (int)config('config.category.lifestyle_others');
            $others = $this->getPosts($locale)
                ->where('category_id', '=', $othersId)
                ->limit(2)
                ->get();
            $category->others = PostWithImageApiResource::collection($others);

            // get op-ed posts
            $post_ids = OpedPost::query()->where('category_id', '=', $categoryId)->pluck('post_id')->toArray();
            $oped = $this->getPosts($locale)->whereIn('id', $post_ids)->limit(10)->get();
            $category->oped = PostOpEdApiResource::collection($oped);
        }

        return CategoryLifestyleApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryEducationApiResource
     */
    protected function getEducationCategoryResponse($locale): CategoryEducationApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.education');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $educationPosts = $this->getPosts($locale)
                ->where('id', '!=', $featured?->id)
                ->where('category_id', '=', $categoryId)
                ->limit(3)
                ->get();
            $category->posts = PostWithImageApiResource::collection($educationPosts);

            // article subcategory
            $articleId = (int)config('config.category.education_article');

            $articles = $this->getPosts($locale)
                ->where('category_id', '=', $articleId)
                ->limit(1)
                ->get();
            $category->articles = PostWithImageApiResource::collection($articles);

            // get op-ed posts
            $post_ids = OpedPost::query()->where('category_id', '=', $categoryId)->pluck('post_id')->toArray();
            $oped = $this->getPosts($locale)->whereIn('id', $post_ids)->limit(2)->get();
            $category->oped = PostOpEdApiResource::collection($oped);
        }

        return CategoryEducationApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategorySportsApiResource
     */
    protected function getSportsCategoryResponse($locale): CategorySportsApiResource
    {
        App::setLocale($locale);

        $categories = json_decode('[' . config('config.category.sports') . ']', true);

        $categoryId = (int)config('config.category.sports_parent');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {                
                $category->featured = PostFeaturedApiResource::make($featured);
            }
            // get latest posts
            $sportPosts = $this->getPosts($locale)
                ->where('id', '!=', $featured?->id)
                ->where('category_id', '=', $categoryId)
                ->limit(2)
                ->get();
            $category->posts = PostWithImageApiResource::collection($sportPosts);

            // Prepare exclude post ids
            $ids =array_merge([$featured?->id], $sportPosts->pluck('id')->toArray());
            $sportMostRead = $this->getPosts($locale)
                ->whereNotIn('id', $ids)
                ->whereIn('category_id', $categories)
                ->limit(4)
                ->get();
            $category->latest = PostWithImageApiResource::collection($sportMostRead);

            $sportMostReadIds = collect($sportPosts)->merge($sportMostRead)->pluck('id')->toArray();
            $ids = array_merge([$featured?->id], $sportMostReadIds);
            $sportTitles = $this->getPosts($locale)
                ->whereNotIn('id', $ids)
                ->whereIn('category_id', $categories)
                ->limit(9)
                ->get();
            $category->titles = PostTitleApiResource::collection($sportTitles);
        }

        return CategorySportsApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryNationalApiResource
     */
    protected function getNationalCategoryResponse($locale): CategoryNationalApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.national');
        $politicsId = (int)config('config.category.national_politics');
        $category_all = json_decode('[' . config('config.category.national_all') . ']', true);

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get politics featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $politicsId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest national posts
            $nationalPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->limit(3)
                ->get();
            $category->national = PostWithTextApiResource::collection($nationalPosts);

            // national country
            $country = (int)config('config.category.national_country');
            $countryPosts = $this->getPosts($locale)
                ->where('category_id', '=', $country)
                ->limit(3)
                ->get();
            // national crime
            $crime = (int)config('config.category.national_crime');
            $crimePosts = $this->getPosts($locale)
                ->where('category_id', '=', $crime)
                ->limit(3)
                ->get();

            $titles = $countryPosts->merge($crimePosts);
            $category->titles = PostTitleApiResource::collection($titles);

            // get op-ed posts
            $post_ids = OpedPost::query()->whereIn('category_id', $category_all)->pluck('post_id')->toArray();
            $oped = $this->getPosts($locale)->whereIn('id', $post_ids)->limit(2)->get();
            $category->oped = PostOpEdApiResource::collection($oped);
        }

        return CategoryNationalApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryTradeApiResource
     */
    protected function getTradeCategoryResponse($locale): CategoryTradeApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.trade');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $tradePosts = $this->getPosts($locale)
                ->where('id', '!=', $featured?->id)
                ->where('category_id', '=', $categoryId)
                ->limit(2)
                ->get();
            $category->posts = PostWithImageApiResource::collection($tradePosts);

            $tradePostsIds = $tradePosts->pluck('id')->toArray();
            $ids = array_merge([$featured?->id], $tradePostsIds);

            $tradeMostRead = $this->getPostsByDateTime($locale)
                ->whereNotIn('id', $ids)
                ->where('category_id', '=', $categoryId)
                ->limit(5)
                ->get();
            $category->mostRead = PostWithImageApiResource::collection($tradeMostRead);

            // get op-ed posts
            $post_ids = OpedPost::query()->where('category_id', '=', $categoryId)->pluck('post_id')->toArray();
            $oped = $this->getPosts($locale)->whereIn('id', $post_ids)->limit(4)->get();
            $category->oped = PostOpEdApiResource::collection($oped);
        }

        return CategoryTradeApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryTradeApiResource
     */
    protected function getBanglaCategoryResponse($locale): CategoryTradeApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.bangla');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $banglaPosts = $this->getPosts($locale)
                ->where('id', '!=', $featured?->id)
                ->where('category_id', '=', $categoryId)
                ->limit(2)
                ->get();
            $category->posts = PostWithImageApiResource::collection($banglaPosts);

            $banglaPostsIds = $banglaPosts->pluck('id')->toArray();
            $ids = array_merge([$featured?->id], $banglaPostsIds);

            $banglaMostRead = $this->getPosts($locale)
                ->whereNotIn('id', $ids)
                ->where('category_id', '=', $categoryId)
                ->limit(4)
                ->get();
            $category->mostRead = PostWithImageApiResource::collection($banglaMostRead);

            // $tradeMostRead = $this->getPostsByViewCount($locale)
            //     ->where('id', '!=', $featured?->id)
            //     ->where('category_id', '=', $categoryId)
            //     ->limit(4)
            //     ->get();
            // $category->mostRead = PostWithImageApiResource::collection($tradeMostRead);

            // get op-ed posts
            $post_ids = OpedPost::query()->where('category_id', '=', $categoryId)->pluck('post_id')->toArray();
            $oped = $this->getPosts($locale)->whereIn('id', $post_ids)->limit(4)->get();
            $category->oped = PostOpEdApiResource::collection($oped);
        }

        return CategoryTradeApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryStockApiResource
     */
    protected function getStockCategoryResponse($locale): CategoryStockApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.stock');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get stock featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest stock posts
            $stockPosts = $this->getPosts($locale)
                ->where('id', '!=', $featured?->id)
                ->where('category_id', '=', $categoryId)
                ->limit(2)
                ->get();
            $category->posts = PostWithImageApiResource::collection($stockPosts);

            // get latest national posts
            $bangladesh = (int)config('config.category.stock_bangladesh');
            $bangladeshPosts = $this->getPosts($locale)
                ->where('category_id', '=', $bangladesh)
                ->limit(3)
                ->get();
            $category->bangladesh = PostWithImageApiResource::collection($bangladeshPosts);

            // get latest national posts
            $global = (int)config('config.category.stock_global');
            $globalPosts = $this->getPosts($locale)
                ->where('category_id', '=', $global)
                ->limit(3)
                ->get();
            $category->global = PostWithImageApiResource::collection($globalPosts);

            // get op-ed posts
            $post_ids = OpedPost::query()->whereIn('category_id', array($categoryId, $bangladesh, $global))->pluck('post_id')->toArray();
            $oped = $this->getPosts($locale)->whereIn('id', $post_ids)->limit(2)->get();
            $category->oped = PostOpEdApiResource::collection($oped);
        }

        return CategoryStockApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryYouthApiResource
     */
    protected function getYouthCategoryResponse($locale): CategoryYouthApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.youth_and_entrepreneurship');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get latest posts
            $youthPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->limit(4)
                ->get();
            $category->posts = PostWithImageApiResource::collection($youthPosts);
        }

        return CategoryYouthApiResource::make($category);
    }

    /**
     * @param $locale
     * @return CategoryPersonalFinanceApiResource
     */
    protected function getPersonalFinanceCategoryResponse($locale): CategoryPersonalFinanceApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.personal_finance');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $tradePosts = $this->getPosts($locale)
                ->where('id', '!=', $featured?->id)
                ->where('category_id', '=', $categoryId)
                ->limit(8)
                ->get();
            $category->posts = PostWithImageApiResource::collection($tradePosts);

            $tradeTitles = $this->getPostsByViewCount($locale)
                ->where('category_id', '=', $categoryId)
                ->limit(8)
                ->get();
            $category->titles = PostTitleApiResource::collection($tradeTitles);
        }

        return CategoryPersonalFinanceApiResource::make($category);
    }

    /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    protected function getMostReadResponse($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        // get most read posts
        $posts = $this->getLastWeekPostsByViewCount($locale)
            ->limit(10)
            ->get();
        ResourceCollection::withoutWrapping();

        return PostWithTextApiResource::collection($posts);
    }

    /**
     * @param $locale
     * @return Builder
     */
    private function getPosts($locale): Builder
    {
        return Post::with([
            'translations',
            'category',
            'category.translations',
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->where('datetime', '<=', now()) 
            // ->whereDate('datetime', '<=', now())
            ->orderByDesc('datetime');
    }

    /**
     * @param $locale
     * @return Builder
     */
    private function getPostsByViewCount($locale): Builder
    {
        return Post::with([
            'translations',
            'category',
            'category.translations',
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
             ->whereDate('datetime', '<=', now())
            ->orderByDesc('views_count');
    }

    /**
     * @param $locale
     * @return Builder
     */
    private function getLastWeekPostsByViewCount($locale): Builder
    {
        $startDate = Carbon::now()->subWeek(1)->startOfWeek(Carbon::SATURDAY);
        $endDate = Carbon::now()->subWeek()->endOfWeek(Carbon::FRIDAY);

        return Post::with([
            'translations',
            'category',
            'category.translations',
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->whereBetween('datetime', [date($startDate), date($endDate)])
            ->orderByDesc('views_count');
    }

    /**
     * @param $locale
     * @return Builder
     */
    private function getPostsByDateTime($locale): Builder
    {
        return Post::with([
            'translations',
            'category',
            'category.translations',
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->where('datetime', '<=', now()) 
            // ->whereDate('datetime', '<=', now())
            ->orderByDesc('datetime');
    }

    /**
     * @param $locale
     * @return Builder
     */
    private function getTodayPostsByViewCount($locale): Builder
    {
        $startDate = Carbon::today()->startOfDay();
        $endDate = $startDate->copy()->endOfDay();

        return Post::with([
            'translations',
            'category',
            'category.translations',
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->whereBetween('datetime', [date($startDate), date($endDate)])
            ->orderByDesc('views_count');
    }

     /**
     * @param $locale
     * @return AnonymousResourceCollection
     */
    protected function getCategoryViewsResponse($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $categories = json_decode('[' . config('config.category.views_all') . ']', true);
        $category_ids = implode(',', $categories);

        $posts = Category::with(['translations', 'posts' => function ($q) use ($locale) {
            $q->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            });
        }])
            ->whereIn('id', $categories)
            ->orderByRaw("FIELD(id, $category_ids)")
            ->get();
        ResourceCollection::withoutWrapping();

        return CategoryMoreApiResource::collection($posts);
    }
}