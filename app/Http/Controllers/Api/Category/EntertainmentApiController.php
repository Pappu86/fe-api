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
use OpenApi\Annotations as OA;
use Carbon\Carbon;

class EntertainmentApiController extends Controller
{
    use OpEdResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/entertainment/home",
     *     summary="Get posts by entertainment category",
     *     description="Get posts by entertainment category",
     *     operationId="entertainmentCategoryApiIndex",
     *     tags={"EntertainmentCategoryApi"},
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
    public function getentertainmentPosts($locale): CategoryParentApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.entertainment');

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
            $entertainmentPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(6)
                ->get();
            $posts = $posts->merge(collect($entertainmentPosts)->pluck('id'));
            // put in cache
            Cache::forever('entertainment_category_posts_' . $locale, $posts->toArray());

            $category->posts = PostWithImageApiResource::collection($entertainmentPosts);
        }
        ResourceCollection::withoutWrapping();

        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/entertainment/more",
     *     summary="Get posts by entertainment more",
     *     description="Get posts by entertainment more by paginate",
     *     operationId="entertainmentCategoryMoreApiIndex",
     *     tags={"EntertainmentCategoryApi"},
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
        $categoryId = (int)config('config.category.entertainment');
        $ids = Cache::get('entertainment_category_posts_' . $locale) ?? array();

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
