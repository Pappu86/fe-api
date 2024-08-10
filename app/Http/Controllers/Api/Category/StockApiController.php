<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Category\CategoryParentApiResource;
use App\Http\Resources\Api\PostFeaturedApiResource;
use App\Http\Resources\Api\PostMoreApiResource;
use App\Http\Resources\Api\PostOpEdApiResource;
use App\Http\Resources\Api\PostTitleApiResource;
use App\Http\Resources\Api\PostWithImageApiResource;
use App\Models\Category;
use App\Models\Post;
use App\Traits\OpEdResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class StockApiController extends Controller
{
    use OpEdResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/stock/home",
     *     summary="Get posts by stock category",
     *     description="Get posts by stock category",
     *     operationId="stockCategoryApiIndex",
     *     tags={"StockCategoryApi"},
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
     * @return CategoryParentApiResource
     */
    public function getStockPosts($locale): CategoryParentApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.stock');

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
            $stockPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(4)
                ->get();
            $posts = $posts->merge(collect($stockPosts)->pluck('id'));
            // put in cache
            Cache::forever('stock_category_posts_' . $locale, $posts->toArray());

            $category->posts = PostWithImageApiResource::collection($stockPosts);
        }
        ResourceCollection::withoutWrapping();

        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/stock/bangladesh",
     *     summary="Get posts by stock bangladesh category",
     *     description="Get posts by stock bangladesh category",
     *     operationId="stockBangladeshCategoryApiIndex",
     *     tags={"StockCategoryApi"},
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
     * @return CategoryParentApiResource
     */
    public function getStockBangladeshPosts($locale): CategoryParentApiResource
    {
        $bangladesh = (int)config('config.category.stock_bangladesh');

        return $this->getStockChildPosts($locale, $bangladesh);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/stock/global",
     *     summary="Get posts by stock global category",
     *     description="Get posts by stock global category",
     *     operationId="stockGlobalCategoryApiIndex",
     *     tags={"StockCategoryApi"},
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
     * @return CategoryParentApiResource
     */
    public function getStockGlobalPosts($locale): CategoryParentApiResource
    {
        $global = (int)config('config.category.stock_global');

        return $this->getStockChildPosts($locale, $global);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/stock/more",
     *     summary="Get posts by stock more",
     *     description="Get posts by stock more by paginate",
     *     operationId="stockCategoryMoreApiIndex",
     *     tags={"StockCategoryApi"},
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
        $categoryId = (int)config('config.category.stock');
        $bangladeshCategoryId = (int)config('config.category.stock_bangladesh');
        $globalCategoryId = (int)config('config.category.stock_global');
        $ids = Cache::get('stock_category_posts_' . $locale) ?? array();

        // $bangladeshPostIds = Cache::get('stock_Bangladesh_category_posts_' . $locale) ?? array(); 
        // $globalPostIds = Cache::get('stock_Global_category_posts_' . $locale) ?? array();
        // $allExcludePostIds=collect([...$ids,...$bangladeshPostIds,...$globalPostIds])->unique()->values()->toArray();

        $posts = $this->getPosts($locale)
            ->whereIn('category_id', [$categoryId,$bangladeshCategoryId,$globalCategoryId])
            ->whereNotIn('id', $ids)
            ->paginate($limit);
            // ->where('category_id', '=', $categoryId)
            // ->whereNotIn('id', $ids)
            

        return response()->json([
            'items' => PostMoreApiResource::collection($posts->items()),
            'nextPageUrl' => $posts->nextPageUrl(),
            'prevPageUrl' => $posts->previousPageUrl(),
            'perPage' => $posts->perPage(),
            'hasPages' => $posts->hasPages()
        ]);
    }

    /**
     * @param $locale
     * @param $categoryId
     * @return CategoryParentApiResource
     */
    private function getStockChildPosts($locale, $categoryId): CategoryParentApiResource
    {
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
            // get general posts
            $bangladeshPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(3)
                ->get();
            $category->posts = PostWithImageApiResource::collection($bangladeshPosts);

            // get op-ed posts
            $op_ed_ids = $this->getOpEdPosts($categoryId);
            $oped = $this->getPosts($locale)->whereIn('id', $op_ed_ids)->limit(2)->get();
            $category->oped = PostOpEdApiResource::collection($oped);

            // get title only posts
            $ids = collect($bangladeshPosts)->merge($oped)->pluck('id')->toArray();
            $titles = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->whereNotIn('id', $ids)
                ->limit(21)
                ->get();
            $category->titles = PostTitleApiResource::collection($titles);

            // put in cache
            $posts = $posts->merge(collect($titles)->pluck('id'), $ids);
            Cache::forever('stock_'.$category->name.'_category_posts_' . $locale, $posts->toArray());
        }
        ResourceCollection::withoutWrapping();

        return CategoryParentApiResource::make($category);
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
}