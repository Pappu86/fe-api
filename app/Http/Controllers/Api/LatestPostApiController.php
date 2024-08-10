<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\LatestPostResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class LatestPostApiController extends Controller
{
    use LatestPostResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/latest-post",
     *     summary="Get latest post",
     *     description="Get latest posts",
     *     operationId="latestPostApiIndex",
     *     tags={"LatestPostApi"},
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
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/LatestPostApi"
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
    public function index($locale): JsonResponse
    {
        $items = Cache::get('latest_post_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->get($locale));
        }
    }
}
