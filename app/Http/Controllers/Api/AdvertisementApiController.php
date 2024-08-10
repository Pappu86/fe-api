<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AdsPositionApiResource;
use App\Models\AdsPosition;
use App\Models\Advertisement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class AdvertisementApiController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/revenue/{page}",
     *     summary="Get ads by page",
     *     description="Get ads by page",
     *     operationId="revenueHomeAds",
     *     tags={"AdvertisementApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter(
     *     description="Page name",
     *     in="path",
     *     name="page",
     *     example="home",
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
     *     @OA\Items(
     *     type="string",
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="401",
     *     ref="#/components/responses/401"
     * ),
     *     @OA\Response (
     *     response="403",
     *     ref="#/components/responses/403"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Display a listing of the resource.
     *
     * @param $page
     * @return AnonymousResourceCollection
     */
    public function getAds($page): AnonymousResourceCollection
    {
        $positions = AdsPosition::with(['ads' => function ($q) {
            $q->where('start_date', '<=', now())
                ->where('end_date', '>', now())
                ->where('status', '=', 1);
        }])
            ->where('status', '=', 1)
            ->where('page', '=', $page)
            ->get();

        return AdsPositionApiResource::collection($positions);
    }

    /**
     * @OA\Get (
     *     path="/api/revenue/global",
     *     summary="Get ads by page",
     *     description="Get ads by page",
     *     operationId="revenueGlobalAds",
     *     tags={"AdvertisementApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     type="string",
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="401",
     *     ref="#/components/responses/401"
     * ),
     *     @OA\Response (
     *     response="403",
     *     ref="#/components/responses/403"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function getGlobalAds(): AnonymousResourceCollection
    {
        $positions = AdsPosition::with(['ads' => function ($q) {
            $q->where('start_date', '<=', now())
                ->where('end_date', '>', now());
        }])
            ->where('status', '=', 1)
            ->where('section', '=', 'global')
            ->get();

        return AdsPositionApiResource::collection($positions);
    }
}
