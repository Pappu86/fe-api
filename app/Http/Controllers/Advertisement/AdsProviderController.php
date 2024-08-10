<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Models\AdsProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AdsProviderController extends Controller
{
    /**
     * @OA\Get (
     *     path="/ads/providers",
     *     summary="Get ads providers",
     *     description="Get ads providers",
     *     operationId="adsProvider",
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
     * @return JsonResponse
     */
    public function getProviders(): JsonResponse
    {
        $providers = AdsProvider::query()
            ->where('status', '=', 1)
            ->select('id as value', 'name as text')
            ->get();

        return response()->json([
            'data' => $providers
        ]);
    }
}
