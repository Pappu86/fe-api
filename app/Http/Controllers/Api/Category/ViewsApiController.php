<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Category\CategoryViewsApiResource;
use App\Http\Resources\Api\Category\CategoryViewsChildApiResource;
use App\Http\Resources\Api\PostFeaturedApiResource;
use App\Http\Resources\Api\PostMoreApiResource;
use App\Http\Resources\Api\PostOpEdApiResource;
use App\Http\Resources\Api\PostTitleApiResource;
use App\Http\Resources\Api\PostWithImageApiResource;
use App\Http\Resources\Api\PostWithoutCategoryApiResource;
use App\Http\Resources\Api\PostWithReporterApiResource;
use App\Models\Category;
use App\Models\Post;
use App\Traits\OpEdResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class ViewsApiController extends Controller
{
    use OpEdResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/home",
     *     summary="Get posts by views category",
     *     description="Get posts by views category",
     *     operationId="viewsCategoryApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @return CategoryViewsApiResource|JsonResponse
     */
    public function getViewsPosts($locale): CategoryViewsApiResource|JsonResponse
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.views');

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
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $viewsPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(5)
                ->get();
            $posts = $posts->merge(collect($viewsPosts)->pluck('id'));
            // put in cache
            Cache::forever('views_category_posts_' . $locale, $posts->toArray());

            $category->posts = PostWithReporterApiResource::collection($viewsPosts);

            ResourceCollection::withoutWrapping();

            return CategoryViewsApiResource::make($category);
        }
        return response()->json(null);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/views",
     *     summary="Get posts by views views category",
     *     description="Get posts by views views category",
     *     operationId="viewsViewsCategoryApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @param $locale
     * @return CategoryViewsApiResource
     */
    public function getViewsViewsPosts($locale): CategoryViewsApiResource
    {
        $views = (int)config('config.category.views_views');

        return $this->getViewsChildPosts($locale, $views);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/reviews",
     *     summary="Get posts by views reviews category",
     *     description="Get posts by views reviews category",
     *     operationId="viewsReviewsCategoryApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @param Request $request
     * @param $locale
     * @return CategoryViewsChildApiResource
     */
    public function getViewsReviewsPosts($locale): CategoryViewsChildApiResource
    {
        $categoryId = (int)config('config.category.views_reviews');

        $category = $this->getFeaturedAndLatest($categoryId, $locale, 6, 'reviews');
        ResourceCollection::withoutWrapping();

        return CategoryViewsChildApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/opinions",
     *     summary="Get posts by views opinions category",
     *     description="Get posts by views opinions category",
     *     operationId="viewsOpinionsCategoryApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @param $locale
     * @return CategoryViewsChildApiResource
     */
    public function getViewsOpinionsPosts($locale): CategoryViewsChildApiResource
    {
        $categoryId = (int)config('config.category.views_opinions');

        $category = $this->getFeaturedAndLatest($categoryId, $locale, 7, 'opinions');
        ResourceCollection::withoutWrapping();

        return CategoryViewsChildApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/columns",
     *     summary="Get posts by views columns category",
     *     description="Get posts by views columns category",
     *     operationId="viewsColumnsCategoryApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @param $locale
     * @return CategoryViewsChildApiResource
     */
    public function getViewsColumnsPosts($locale): CategoryViewsChildApiResource
    {
        $categoryId = (int)config('config.category.views_columns');

        $category = $this->getFeaturedAndLatest($categoryId, $locale, 7, 'columns');
        ResourceCollection::withoutWrapping();

        return CategoryViewsChildApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/analysis",
     *     summary="Get posts by views analysis category",
     *     description="Get posts by views analysis category",
     *     operationId="viewsAnalysisCategoryApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @param $locale
     * @return CategoryViewsChildApiResource
     */
    public function getViewsAnalysisPosts($locale): CategoryViewsChildApiResource
    {
        $categoryId = (int)config('config.category.views_analysis');

        $category = $this->getFeaturedAndLatest($categoryId, $locale, 2, 'analysis');
        ResourceCollection::withoutWrapping();

        return CategoryViewsChildApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/letters",
     *     summary="Get posts by views letters category",
     *     description="Get posts by views letters category",
     *     operationId="viewsLettersCategoryApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @param $locale
     * @return CategoryViewsChildApiResource
     */
    public function getViewsLettersPosts($locale): CategoryViewsChildApiResource
    {
        $categoryId = (int)config('config.category.views_letters');

        $category = $this->getFeaturedAndLatest($categoryId, $locale, 2, 'letters');
        ResourceCollection::withoutWrapping();

        return CategoryViewsChildApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/economic-trends-and-insights",
     *     summary="Get posts by views economic trends and insights category",
     *     description="Get posts by views economic trends and insights category",
     *     operationId="viewsEconomicTrendsCategoryApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @param $locale
     * @return CategoryViewsChildApiResource
     */
    public function getViewsEconomicTrendsPosts($locale): CategoryViewsChildApiResource
    {
        $categoryId = (int)config('config.category.views_economictrends');

        $category = $this->getFeaturedAndLatest($categoryId, $locale, 6, 'economictrends');
        ResourceCollection::withoutWrapping();

        return CategoryViewsChildApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/more",
     *     summary="Get posts by views more",
     *     description="Get posts by views more by paginate",
     *     operationId="viewsCategoryMoreApiIndex",
     *     tags={"ViewsCategoryApi"},
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
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryMorePosts(Request $request, $locale): JsonResponse
    {
        $limit = (int)$request->query('limit', 12);
        $categoryId = (int)config('config.category.views');
        $ids = Cache::get('views_category_posts_' . $locale) ?? array();

        $posts = $this->getPosts($locale)
            ->where('category_id', '=', $categoryId)
            ->whereNotIn('id', $ids)
            ->paginate($limit);

        return response()->json([
            'items' => PostMoreApiResource::collection($posts->items()),
            'nextPageUrl' => $posts->nextPageUrl(),
            'prevPageUrl' => $posts->previousPageUrl(),
            'perPage' => $posts->perPage(),
            'hasPages' => $posts->hasPages()
        ]);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/views/sub-more/{category}/{category_id}",
     *     summary="Get posts by views subcategory more",
     *     description="Get posts by views more by paginate",
     *     operationId="viewsSubCategoryMoreApiIndex",
     *     tags={"ViewsCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Category key",
     *     in="path",
     *     name="category",
     *     required=true,
     *     example="reviews",
     *     @OA\Schema(
     *     type="string",
     * )
     * ),
     *     @OA\Parameter(
     *     description="Category Id",
     *     in="path",
     *     name="category_id",
     *     required=true,
     *     example="1",
     *     @OA\Schema(
     *       type="integer",
     *       format="int64"
     * )
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
     * @param Request $request
     * @param $locale
     * @param $category
     * @param $category_id
     * @return JsonResponse
     */
    public function getSubCategoryMorePosts(Request $request, $locale, $category, $category_id): JsonResponse
    {
        $limit = (int)$request->query('limit', 4);
        $ids = Cache::get('views_subcategory_' . $category . '_' . $locale) ?? array();

        $posts = $this->getPosts($locale)
            ->where('category_id', '=', $category_id)
            ->whereNotIn('id', $ids)
            ->orderByDesc('id')
            ->cursorPaginate($limit);

        return response()->json([
            'items' => PostWithReporterApiResource::collection($posts->items()),
            'nextPageUrl' => $posts->nextPageUrl(),
            'prevPageUrl' => $posts->previousPageUrl(),
            'limit' => $posts->perPage(),
            'hasPages' => $posts->hasMorePages()
        ]);
    }

    /**
     * @param $locale
     * @param $categoryId
     * @return CategoryViewsApiResource
     */
    private function getViewsChildPosts($locale, $categoryId): CategoryViewsApiResource
    {
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
            // get general posts
            $bangladeshPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(3)
                ->get();
            $category->posts = PostWithImageApiResource::collection($bangladeshPosts);

            // get most read posts
            $childMostRead = $this->getPostsByViewCount($locale)
                ->where('category_id', '=', $categoryId)
                ->limit(2)
                ->get();
            $category->mostread = PostWithImageApiResource::collection($childMostRead);

            // get title only posts
            $ids = collect($bangladeshPosts)->merge($childMostRead)->pluck('id')->toArray();
            $titles = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->whereNotIn('id', $ids)
                ->limit(21)
                ->get();
            $category->titles = PostTitleApiResource::collection($titles);
        }
        ResourceCollection::withoutWrapping();

        return CategoryViewsApiResource::make($category);
    }

    /**
     * @param $categoryId
     * @param $locale
     * @param $limit
     * @param $key
     * @return Model|Builder|null
     */
    private function getFeaturedAndLatest($categoryId, $locale, $limit, $key): Model|Builder|null
    {
        $category = Category::with('translations')
            ->where('id', '=', $categoryId)
            ->first();
        if ($category) {
            // need to store in cache
            $ids = collect();
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $categoryId)
                ->first();
            if ($featured) {
                $ids->push($featured->id);
                $category->featured = PostWithReporterApiResource::make($featured);
            }

            // get latest posts
            $posts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit($limit)
                ->get();
            $ids = $ids->merge(collect($posts)->pluck('id'));
            // put in cache
            Cache::forever('views_subcategory_' . $key . '_' . $locale, $ids->toArray());

            $category->posts = PostWithReporterApiResource::collection($posts);
        }

        return $category;
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
     * @param $locale
     * @return Builder
     */
    private function getPostsWithReporter($locale): Builder
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
            //->whereDate('datetime', '<=', now())
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
            ->where('datetime', '<=', now())
            // ->whereDate('datetime', '<=', now())
            ->orderByDesc('views_count');
    }
}