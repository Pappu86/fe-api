<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\SliderResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

class SliderApiController extends Controller
{
    use SliderResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/slider",
     *     summary="Get sliders",
     *     description="Get sliders",
     *     operationId="sliderApiIndex",
     *     tags={"SliderApi"},
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
     *     ref="#/components/schemas/SliderApi"
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
     * @return JsonResponse
     */
    public function index($locale): JsonResponse
    {
        return response()->json($this->getSliders($locale));

        // $items = Cache::get('slider_' . $locale);
        // if ($items !== null) {
        //     return response()->json($items);
        // } else {
        //     return response()->json($this->getSliders($locale));
        // }
    }
}
