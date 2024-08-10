<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LiveMediaApiResource;
use App\Traits\LiveMediaResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class LiveMediaApiController extends Controller
{
    use LiveMediaResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/live-media/home",
     *     summary="Get live media",
     *     description="Get live media for home page",
     *     operationId="liveMediaApiHome",
     *     tags={"LiveMediaApi"},
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
     *     ref="#/components/schemas/LiveMediaApi"
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
    public function home($locale): JsonResponse
    {
        $items = Cache::get('media_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->get($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/live-media/featured",
     *     summary="Get featured live media",
     *     description="Get featured live media for home page",
     *     operationId="liveMediaApiFeatured",
     *     tags={"LiveMediaApi"},
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
     *     type="object",
     *     ref="#/components/schemas/LiveMediaApi"
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
    public function featured($locale): JsonResponse
    {
        $item = Cache::get('media_featured_' . $locale);
        if ($item !== null) {
            return response()->json($item);
        } else {
            return response()->json($this->getFeatured($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/live-media/get",
     *     summary="Get live media with paginate",
     *     description="Get live media with paginate",
     *     operationId="liveMediaApiIndex",
     *     tags={"LiveMediaApi"},
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
     *     property="items",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/LiveMediaApi"
     * )
     * ),
     *     @OA\Property(property="nextPageUrl", type="string"),
     *     @OA\Property(property="prevPageUrl", type="string"),
     *     @OA\Property(property="perPage", type="integer"),
     *     @OA\Property(property="hasPages", type="boolean"),
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
        $items = $this->getWithPaginate($locale);

        return response()->json([
            'items' => LiveMediaApiResource::collection($items->items()),
            'nextPageUrl' => $items->nextPageUrl(),
            'prevPageUrl' => $items->previousPageUrl(),
            'perPage' => $items->perPage(),
            'hasPages' => $items->hasPages()
        ]);
    }
}
