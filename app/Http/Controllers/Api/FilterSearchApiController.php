<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ReporterAllApiResource;
use App\Http\Resources\Post\CategoryTreeResource;
use App\Models\Category;
use App\Models\Reporter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use OpenApi\Annotations as OA;

class FilterSearchApiController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/{locale}/filter/reporters",
     *     summary="Get reporters for search filter",
     *     description="Get reporters for search filter",
     *     operationId="searchRepoterApi",
     *     tags={"SearchApi"},
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
     *     ref="#/components/schemas/FilterResponse"
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
    public function getReporters($locale): JsonResponse
    {
        App::setLocale($locale);

        $reporters = Reporter::query()
            ->where('status', '=', 1)
            ->get();

        return response()->json(ReporterAllApiResource::collection($reporters));
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/filter/categories",
     *     summary="Get categories for search filter",
     *     description="Get categories for search filter",
     *     operationId="searchCategoryApi",
     *     tags={"SearchApi"},
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
     *     ref="#/components/schemas/CategoryTreeResponse"
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
    public function getCategories($locale): JsonResponse
    {
        App::setLocale($locale);

        $categories = Category::with(['translations', 'children' => function ($query) {
            $query->with('translations')
                ->where('status', '=', 1);
        }])->whereNull('parent_id')
            ->where('status', '=', 1)
            ->get();

        return response()->json(CategoryTreeResource::collection($categories));
    }
}
