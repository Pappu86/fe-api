<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\Category\CategoryParentApiResource;
use App\Http\Resources\Api\PostFeaturedApiResource;
use App\Http\Resources\Api\PostMoreApiResource;
use App\Http\Resources\Api\PostTitleApiResource;
use App\Http\Resources\Api\PostWithImageApiResource;
use App\Http\Resources\Api\PostWithReporterApiResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class LifestyleApiController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/{locale}/category/lifestyle/home",
     *     summary="Get posts by lifestyle category",
     *     description="Get posts by lifestyle category",
     *     operationId="lifestyleCategoryApiIndex",
     *     tags={"LifestyleCategoryApi"},
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
    public function getLifeStylePosts($locale): CategoryParentApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.lifestyle');

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
            $worldPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(8)
                ->get();
            $posts = $posts->merge(collect($worldPosts)->pluck('id'));
            // put in cache
            Cache::forever('lifestyle_category_posts_' . $locale, $posts->toArray());

            $category->posts = PostWithImageApiResource::collection($worldPosts);
        }
        ResourceCollection::withoutWrapping();

        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/lifestyle/entertainment",
     *     summary="Get posts by lifestyle entertainment category",
     *     description="Get posts by lifestyle entertainment category",
     *     operationId="lifestyleCategoryEntertainmentApiIndex",
     *     tags={"LifestyleCategoryApi"},
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
    public function getEntertainmentPosts($locale): CategoryParentApiResource
    {
        $categoryId = (int)config('config.category.lifestyle_entertainment');

        $category= $this->getLifestyleChildPosts($locale, $categoryId, 5);
        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/lifestyle/living",
     *     summary="Get posts by lifestyle living category",
     *     description="Get posts by lifestyle living category",
     *     operationId="lifestyleCategoryLivingApiIndex",
     *     tags={"LifestyleCategoryApi"},
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
    public function getLivingPosts($locale): CategoryParentApiResource
    {
        $categoryId = (int)config('config.category.lifestyle_living');

        $category = $this->getLifestyleChildPosts($locale, $categoryId, '');
        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/lifestyle/food",
     *     summary="Get posts by lifestyle food category",
     *     description="Get posts by lifestyle food category",
     *     operationId="lifestyleCategoryFoodApiIndex",
     *     tags={"LifestyleCategoryApi"},
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
    public function getFoodPosts($locale): CategoryParentApiResource
    {
        $categoryId = (int)config('config.category.lifestyle_food');

        $category = $this->getLifestyleChildPosts($locale, $categoryId, 12);
        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/lifestyle/gallery",
     *     summary="Get posts by lifestyle gallery category",
     *     description="Get posts by lifestyle gallery category",
     *     operationId="lifestyleCategoryGalleryApiIndex",
     *     tags={"LifestyleCategoryApi"},
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
    public function getGalleryPosts($locale): CategoryParentApiResource
    {
        $categoryId = (int)config('config.category.lifestyle_gallery');

        $category = $this->getLifestyleChildPosts($locale, $categoryId, 7);
        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/lifestyle/culture",
     *     summary="Get posts by lifestyle culture category",
     *     description="Get posts by lifestyle culture category",
     *     operationId="lifestyleCategoryCultureApiIndex",
     *     tags={"LifestyleCategoryApi"},
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
    public function getCulturePosts($locale): CategoryParentApiResource
    {
        $categoryId = (int)config('config.category.lifestyle_culture');

        $category = $this->getLifestyleChildPosts($locale, $categoryId, '');
        return CategoryParentApiResource::make($category);
    }

     /**
     * @OA\Get (
     *     path="/api/{locale}/category/lifestyle/others",
     *     summary="Get posts by lifestyle others category",
     *     description="Get posts by lifestyle others category",
     *     operationId="lifestyleCategoryOtherseApiIndex",
     *     tags={"LifestyleCategoryApi"},
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
    public function getOthersPosts($locale): CategoryParentApiResource
    {
        $categoryId = (int)config('config.category.lifestyle_others');

        $category = $this->getLifestyleChildPosts($locale, $categoryId, 6);
        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/lifestyle/more",
     *     summary="Get posts by lifestyle more",
     *     description="Get posts by lifestyle more by paginate",
     *     operationId="LifestyleCategoryMoreApiIndex",
     *     tags={"WorldCategoryApi"},
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
        $categoryId = (int)config('config.category.lifestyle');
        $ids = Cache::get('world_category_posts_' . $locale) ?? array();

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
     * @param $locale
     * @param $categoryId
     * @return CategoryParentApiResource
     */
    private function getLifestyleChildPosts($locale, $categoryId, $limit=3): CategoryParentApiResource
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
            if(!$limit) $limit = 3;
            
            // get general posts
            $bangladeshPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit($limit)
                ->get();
            $category->posts = PostWithImageApiResource::collection($bangladeshPosts);
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
}