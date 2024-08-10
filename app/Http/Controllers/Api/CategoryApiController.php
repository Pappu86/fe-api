<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Category\CategoryEconomyApiResource;
use App\Http\Resources\Api\PostFeaturedApiResource;
use App\Http\Resources\Api\PostMoreApiResource;
use App\Http\Resources\Api\PostWithImageApiResource;
use App\Http\Resources\Api\SubCategoryHomeApiResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use OpenApi\Annotations as OA;
use Carbon\Carbon;

class CategoryApiController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/{locale}/subcategory/home",
     *     summary="Get posts by subcategory",
     *     description="Get posts by subcategory",
     *     operationId="subcategoryApiIndex",
     *     tags={"SubCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Category slug",
     *     in="query",
     *     name="slug",
     *     required=true,
     *     example="economy/bangladesh",
     *     @OA\Schema(
     *     type="string",
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
     * @return SubCategoryHomeApiResource|JsonResponse
     */
    public function getHomePosts(Request $request, $locale): SubCategoryHomeApiResource|JsonResponse
    {
        App::setLocale($locale);
        $slug = $request->query('slug');

        $category = Category::with('translations', 'parent')
            ->whereHas('translations', function (Builder $q) use ($locale, $slug) {
                $q->where('locale', '=', $locale)
                    ->where('slug', '=', $slug);
            })
            ->first();
        if ($category) {
            // need to store in cache
            $posts = collect();
            // get featured post
            $featured = $this->getPosts($locale)
                ->where('type', '=', 'featured')
                ->where('category_id', '=', $category->id)
                ->first();
            if ($featured) {
                $posts->push($featured->id);
                $category->featured = PostFeaturedApiResource::make($featured);
            }

            // get latest posts
            $collections = $this->getPosts($locale)
                ->where('category_id', '=', $category->id)
                ->where('id', '!=', $featured?->id)
                ->limit(12)
                ->get();
            $posts = $posts->merge(collect($collections)->pluck('id'));
            // put in cache
            Cache::forever('subcategory_' . $slug . '_posts_' . $locale, $posts->toArray());

            $category->posts = PostWithImageApiResource::collection($collections);

            ResourceCollection::withoutWrapping();

            return SubCategoryHomeApiResource::make($category);
        } else {
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/subcategory/more",
     *     summary="Get posts by subcategory with paginate",
     *     description="Get posts by subcategory with paginate",
     *     operationId="subcategoryMoreApiIndex",
     *     tags={"SubCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Category slug",
     *     in="query",
     *     name="slug",
     *     required=true,
     *     example="economy/bangladesh",
     *     @OA\Schema(
     *     type="string",
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
     * @return JsonResponse
     */
    public function getMorePosts(Request $request, $locale): JsonResponse
    {
        App::setLocale($locale);

        $slug = $request->query('slug');
        $limit = (int)$request->query('limit', 12);
        $ids = Cache::get('subcategory_' . $slug . '_posts_' . $locale) ?? array();

        $category = Category::with('translations')
            ->whereHas('translations', function (Builder $q) use ($locale, $slug) {
                $q->where('locale', '=', $locale)
                    ->where('slug', '=', $slug);
            })
            ->first();
        if ($category) {
            $posts = $this->getPosts($locale)
                ->where('category_id', '=', $category->id)
                ->whereNotIn('id', $ids)
                ->paginate($limit);

            return response()->json([
                'items' => PostMoreApiResource::collection($posts->items()),
                'nextPageUrl' => $posts->nextPageUrl(),
                'prevPageUrl' => $posts->previousPageUrl(),
                'perPage' => $posts->perPage(),
                'hasPages' => $posts->hasMorePages()
            ]);
        } else {
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
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
