<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\AdsPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AdsPositionController extends Controller
{

    /**
     * @OA\Get (
     *     path="/ads/pages",
     *     summary="Get ads pages",
     *     description="Get ads pages",
     *     operationId="adsPage",
     *     tags={"Advertisement"},
     *     security={ {"sanctum": {} }},
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
     * @return JsonResponse
     */
    public function getPages(): JsonResponse
    {
        $pages = AdsPosition::query()
            ->where('status', '=', 1)
            ->pluck('page')
            ->unique()
            ->values()
            ->all();

        return response()->json([
            'data' => $pages
        ]);
    }

    /**
     * @OA\Get (
     *     path="/ads/sections/{page}",
     *     summary="Get ads sections by page",
     *     description="Get ads sections by page",
     *     operationId="adsSection",
     *     tags={"Advertisement"},
     *     security={ {"sanctum": {} }},
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
     * @return JsonResponse
     */
    public function getSections($page): JsonResponse
    {
        $sections = AdsPosition::query()
            ->where('status', '=', 1)
            ->where('page', '=', $page)
            ->pluck('section')
            ->unique()
            ->values()
            ->all();

        return response()->json([
            'data' => $sections
        ]);
    }

    /**
     * @OA\Get (
     *     path="/ads/positions/{page}/{section}",
     *     summary="Get ads position by page",
     *     description="Get ads positions by page and section",
     *     operationId="adsPosition",
     *     tags={"Advertisement"},
     *     security={ {"sanctum": {} }},
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
     *     @OA\Parameter(
     *     description="Section name",
     *     in="path",
     *     name="section",
     *     example="hero",
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
     *     ref="#/components/schemas/FilterResponse"
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
     * @param $section
     * @return JsonResponse
     */
    public function getPositions($page, $section): JsonResponse
    {
        $positions = AdsPosition::query()
            ->where('status', '=', 1)
            ->where('page', '=', $page)
            ->where('section', '=', $section)
            ->select('id as value', 'name as text')
            ->get();

        return response()->json([
            'data' => $positions
        ]);
    }
}
