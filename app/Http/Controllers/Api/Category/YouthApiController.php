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
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class YouthApiController extends Controller
{
    use OpEdResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/youth-and-entrepreneurship/home",
     *     summary="Get posts by youth and entrepreneurship category",
     *     description="Get posts by youth and entrepreneurship category",
     *     operationId="YouthAndEntrepreneurshipCategoryApiIndex",
     *     tags={"YouthAndEntrepreneurshipCategoryApi"},
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
    public function getYouthAndEntrepreneurshipPosts($locale): CategoryParentApiResource
    {
        App::setLocale($locale);

        $categoryId = (int)config('config.category.youth_and_entrepreneurship');

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
            $youthPosts = $this->getPosts($locale)
                ->where('category_id', '=', $categoryId)
                ->where('id', '!=', $featured?->id)
                ->limit(4)
                ->get();
            $posts = $posts->merge(collect($youthPosts)->pluck('id'));
            // put in cache
            Cache::forever('youth_and_entrepreneurship_category_posts_' . $locale, $posts->toArray());

            $category->posts = PostWithImageApiResource::collection($youthPosts);
        }
        ResourceCollection::withoutWrapping();

        return CategoryParentApiResource::make($category);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/youth-and-entrepreneurship/youth",
     *     summary="Get posts by youth and entrepreneurship youth category",
     *     description="Get posts by youth and entrepreneurship youth category",
     *     operationId="YouthAndEntrepreneurshipYouthCategoryApiIndex",
     *     tags={"YouthAndEntrepreneurshipCategoryApi"},
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
    public function getYouthAndEntrepreneurshipYouthPosts($locale): CategoryParentApiResource
    {
        $asia = (int)config('config.category.youth_and_entrepreneurship_youth');

        return $this->getWorldChildPosts($locale, $asia);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/youth-and-entrepreneurship/entrepreneurship",
     *     summary="Get posts by youth and entrepreneurship entrepreneurship category",
     *     description="Get posts by youth and entrepreneurship entrepreneurship category",
     *     operationId="YouthAndEntrepreneurshipentrepreneurshipCategoryApiIndex",
     *     tags={"YouthAndEntrepreneurshipCategoryApi"},
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
    public function getYouthAndEntrepreneurshipEntrepreneurshipPosts($locale): CategoryParentApiResource
    {
        $america = (int)config('config.category.youth_and_entrepreneurship_entrepreneurship');

        return $this->getWorldChildPosts($locale, $america);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/youth-and-entrepreneurship/startups",
     *     summary="Get posts by youth and entrepreneurship startups category",
     *     description="Get posts by youth and entrepreneurship startups category",
     *     operationId="YouthAndEntrepreneurshipStartupsCategoryApiIndex",
     *     tags={"YouthAndEntrepreneurshipCategoryApi"},
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
    public function getYouthAndEntrepreneurshipStartupsPosts($locale): CategoryParentApiResource
    {
        $europe = (int)config('config.category.youth_and_entrepreneurship_startups');

        return $this->getWorldChildPosts($locale, $europe);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/category/youth-and-entrepreneurship/more",
     *     summary="Get posts by youth and entrepreneurship more",
     *     description="Get posts by youth and entrepreneurship more by paginate",
     *     operationId="YouthAndEntrepreneurshipCategoryMoreApiIndex",
     *     tags={"YouthAndEntrepreneurshipCategoryApi"},
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
        $categoryId = (int)config('config.category.youth_and_entrepreneurship');
        $startupsCategoryId = (int)config('config.category.youth_and_entrepreneurship_startups');        
        $ids = Cache::get('youth_and_entrepreneurship_category_posts_' . $locale) ?? array();

        $posts = $this->getPosts($locale)
            ->whereIn('category_id', [$categoryId,$startupsCategoryId])
            // ->where('category_id', '=', $categoryId)
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
    private function getWorldChildPosts($locale, $categoryId): CategoryParentApiResource
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
