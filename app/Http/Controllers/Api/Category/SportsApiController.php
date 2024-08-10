<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Category\CategoryParentApiResource;
use App\Http\Resources\Api\Category\CategorySportsChildApiResource;
use App\Http\Resources\Api\Category\CategorySportsPageApiResource;
use App\Http\Resources\Api\Category\CategorySliderApiResource;
use App\Http\Resources\Api\PostFeaturedApiResource;
use App\Http\Resources\Api\PostMoreApiResource;
use App\Http\Resources\Api\PostTitleApiResource;
use App\Http\Resources\Api\PostWithReporterApiResource;
use App\Models\Category;
use App\Models\Post;
use App\Traits\OpEdResponse;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class SportsApiController extends Controller
{
    use OpEdResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/sports/home",
     *     summary="Get posts by sports category",
     *     description="Get posts by sports category",
     *     operationId="sportsCategoryApiIndex",
     *     tags={"SportsCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/PostApi"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * @param $locale
     * @return CategorySportsPageApiResource
     */
    public function getSportsPosts($locale): CategorySportsPageApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.sports_parent');

        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // need to store in cache
            $posts = collect();
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $posts->push($featured->id);
                $category->featured = PostWithReporterApiResource::make($featured);
            }

            // get displayed posts
            $categories = json_decode('[' . config('config.category.sports') . ']', true);
            $displayedPosts = $this->getPosts($locale)
                ->where('type', '=', 'displayed')
                ->whereIn('category_id', $categories)
                ->limit(5)
                ->get();
            $posts = $posts->merge(collect($displayedPosts)->pluck('id'));
            $category->displayed = PostWithReporterApiResource::collection($displayedPosts);

            // get latest posts
            $sportsPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->whereNotIn('id', $posts)
                ->limit(4)
                ->get();
            $posts = $posts->merge(collect($sportsPosts)->pluck('id'));
            $category->posts = PostWithReporterApiResource::collection($sportsPosts);

            // get titles only
            $titles = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->whereNotIn('id', $posts)
                ->limit(4)
                ->get();
            $posts = $posts->merge(collect($titles)->pluck('id'));
            // put in cache
            Cache::forever('sports_category_posts_' . $locale, $posts->toArray());
            $category->titles = PostTitleApiResource::collection($titles);
        }
        ResourceCollection::withoutWrapping();

        return CategorySportsPageApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/sports/cricket",
     *     summary="Get posts by sports category",
     *     description="Get posts by sports category",
     *     operationId="sportsCategoryApiCricket",
     *     tags={"SportsCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/PostApi"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * @param $locale
     * @return CategorySportsChildApiResource
     */
    public function getCricketPosts($locale): CategorySportsChildApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.sports_cricket');

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
                $category->featured = PostWithReporterApiResource::make($featured);
            }

            // get latest posts
            $posts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->cursorPaginate(10);

            $category->posts = [
                'items' => PostWithReporterApiResource::collection($posts->items()),
                'nextPageUrl' => $posts->nextPageUrl(),
                'prevPageUrl' => $posts->previousPageUrl(),
                'perPage' => $posts->perPage(),
                'hasPages' => $posts->hasMorePages()
            ];
        }
        ResourceCollection::withoutWrapping();

        return CategorySportsChildApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/sports/football",
     *     summary="Get posts by sports category",
     *     description="Get posts by sports category",
     *     operationId="sportsCategoryApiFootball",
     *     tags={"SportsCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/PostApi"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * @param $locale
     * @return CategorySportsChildApiResource
     */
    public function getFootballPosts($locale): CategorySportsChildApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.sports_football');

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
                $category->featured = PostWithReporterApiResource::make($featured);
            }

            // get latest posts
            $posts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->cursorPaginate(10);

            $category->posts = [
                'items' => PostWithReporterApiResource::collection($posts->items()),
                'nextPageUrl' => $posts->nextPageUrl(),
                'prevPageUrl' => $posts->previousPageUrl(),
                'perPage' => $posts->perPage(),
                'hasPages' => $posts->hasMorePages()
            ];
        }
        ResourceCollection::withoutWrapping();

        return CategorySportsChildApiResource::make($category);
    }


    /**
     * @OA\Get (
     *     path="/api/{locale}/category/sports/more",
     *     summary="Get posts by sports more",
     *     description="Get posts by sports more category",
     *     operationId="sportsCategoryMoreApiIndex",
     *     tags={"SportsCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/PostApi"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * @param $locale
     * @return CategorySportsPageApiResource
     */
    public function getCategoryMorePosts($locale): CategorySportsPageApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.sports_more_sports');

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
                $category->featured = PostWithReporterApiResource::make($featured);
            }

            // get latest posts
            $posts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(6)
                ->get();

            $category->posts = PostWithReporterApiResource::collection($posts);
        }
        ResourceCollection::withoutWrapping();

        return CategorySportsPageApiResource::make($category);
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
            'reporter',
            'reporter.translations'
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
     * @OA\Get (
     *     path="/api/{locale}/category/sports/slider",
     *     summary="Get slider images by category",
     *     description="Get slider images by category",
     *     operationId="sportsCategorySliderApiIndex",
     *     tags={"SportsCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/PostApi"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * @param $locale
     * @return JsonResponse
     */
    public function getSportsSliderImages($locale): JsonResponse
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.sports_category_slider');

        $category = Asset::with('categories')
            ->whereHas('categories', function (Builder $q) use ($categoryId){
                $q->where('asset_category_id', '=', $categoryId);
            })
            ->limit(6)
            ->get();

            return response()->json(CategorySliderApiResource::collection($category));
    }
}