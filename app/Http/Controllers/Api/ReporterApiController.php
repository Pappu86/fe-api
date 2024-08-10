<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reporter;
use App\Models\Post;
use App\Http\Resources\Api\PostApiResource;
use App\Http\Resources\Api\PostMoreApiResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Api\PostWithImageApiResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;
use Carbon\Carbon;

class ReporterApiController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/{locale}/reporter/{username}",
     *     summary="Get Reporter",
     *     description="Get Reporter",
     *     operationId="reporterApiShow",
     *     tags={"ReporterApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Reporter username",
     *     in="path",
     *     name="username",
     *     required=true,
     *     @OA\Schema(
     *       type="string",
     *    )
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
     * @param $username
     * @return JsonResponse
     */
    public function show($locale, $username): JsonResponse
    {
        App::setLocale($locale);

        // $post = Reporter::with([
        //     'translations',
        // ])
        //     ->whereHas('translations', function (Builder $q) use ($locale) {
        //         $q->where('locale', '=', $locale);
        //     })
        //     ->where('status', '=', 1)
        //     ->where('username', '=', $username)
        //     ->first();
        // ResourceCollection::withoutWrapping();

        // if ($post) {
        //     return response()->json($post);
        // } else {
        //     return null;
        // }


        $category = Reporter::with([
            'translations',
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->where('status', '=', 1)
            ->where('username', '=', $username)
            ->first();
        
        
        if ($category) {
            // need to store in cache
            $posts = collect();
            
            // get latest posts
            $reporterPosts = $this->getPosts($locale)
                ->where('reporter_id', '=', $category->id)
                ->limit(8)
                ->get();
            $posts = $posts->merge(collect($reporterPosts)->pluck('id'));
            // put in cache
            Cache::forever('reporter_posts_' . $locale, $posts->toArray());

            $category->posts = PostWithImageApiResource::collection($reporterPosts);
        }
        ResourceCollection::withoutWrapping();

        
        return response()->json($category);
        
    }
    
    /**
     * @OA\Get (
     *     path="/api/{locale}/reporter/{username}/more",
     *     summary="Get Reporter more post",
     *     description="Get Reporter more post",
     *     operationId="reporterMoreApiShow",
     *     tags={"ReporterApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Reporter username",
     *     in="path",
     *     name="username",
     *     required=true,
     *     @OA\Schema(
     *       type="string",
     *    )
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
     * @param $username
     * @return JsonResponse
     */
    public function getReporterMorePosts(Request $request, $locale, $username): JsonResponse
    {
        $limit = (int)$request->query('limit', 12);
        $ids = Cache::get('reporter_posts_' . $locale) ?? array();

        $category = Reporter::where('username', '=', $username)
            ->first();
        
        if($ids){
            $posts = $this->getPosts($locale)
            ->where('reporter_id', '=', $category->id)
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
