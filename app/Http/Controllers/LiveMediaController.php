<?php

namespace App\Http\Controllers;

use App\Http\Resources\LiveMediaEditResource;
use App\Http\Resources\LiveMediaResource;
use App\Jobs\Cache\LiveMedia\CacheLiveMediaFeaturedResponse;
use App\Jobs\Cache\LiveMedia\CacheLiveMediaResponse;
use App\Models\LiveMedia;
use App\Traits\Image;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use OpenApi\Annotations as OA;
use Throwable;

class LiveMediaController extends Controller
{
    use Image;

    /**
     * @OA\Get (
     *     path="/{locale}/live-media",
     *     summary="Get live media",
     *     description="Get live media with paginate",
     *     operationId="liveMediaIndex",
     *     tags={"LiveMedia"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/query"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/sortBy"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/direction"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/per_page"
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/LiveMedia"
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
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $this->authorize('viewAny media');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $medias = LiveMedia::query()->latest();
        if ($query) {
            $medias = LiveMedia::query()->whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $medias = LiveMedia::query()->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $medias->get();
            $medias = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $medias = $medias->paginate($per_page);
        }

        return LiveMediaResource::collection($medias);
    }

    /**
     * @OA\Post (
     *     path="/{locale}/live-media",
     *     summary="Store live media",
     *     description="Store live media",
     *     operationId="liveMediaStore",
     *     tags={"LiveMedia"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\RequestBody (
     *     required=true,
     *     description="Please enter valid information",
     *     @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              ref="#/components/schemas/LiveMedia"
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="liveMediaId", type="integer", example="1")
     *     ),
     * ),
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request, $locale): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('create media');

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'featured' => filter_var($request->input('featured'), FILTER_VALIDATE_BOOLEAN),
            ]);
            $media = LiveMedia::query()->create($request->except('image'));

            $this->saveImage($request, $media);

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'liveMediaId' => $media->id
            ], 201);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Get (
     *     path="/{locale}/live-media/{id}",
     *     summary="Get live media",
     *     description="Get live media",
     *     operationId="liveMediaShow",
     *     tags={"LiveMedia"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/id"
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="object",
     *     ref="#/components/schemas/LiveMedia"
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
     *     response="404",
     *     ref="#/components/responses/404"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Display the specified resource.
     *
     * @param $locale
     * @param LiveMedia $live_medium
     * @return LiveMediaEditResource
     */
    public function show($locale, LiveMedia $live_medium): LiveMediaEditResource
    {
        App::setLocale($locale);

        return LiveMediaEditResource::make($live_medium);
    }

    /**
     * @OA\Patch (
     *     path="/{locale}/live-media/{id}",
     *     summary="Update live media",
     *     description="Update live media",
     *     operationId="liveMediaUpdate",
     *     tags={"LiveMedia"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/id"
     * ),
     *     @OA\RequestBody (
     *     required=true,
     *     description="Please enter valid information",
     *     @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              ref="#/components/schemas/LiveMedia"
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="200",
     *     ref="#/components/responses/200"
     * ),
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
     *     response="404",
     *     ref="#/components/responses/404"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $locale
     * @param LiveMedia $live_medium
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, LiveMedia $live_medium): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update media');

        // begin database transaction
        DB::beginTransaction();
        try {
            $featured = filter_var($request->input('featured'), FILTER_VALIDATE_BOOLEAN);
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'featured' => $featured,
            ]);
            $live_medium->update($request->except('image'));

            $this->saveImage($request, $live_medium);

            // commit changes
            DB::commit();
            if ($featured) {
                // cache featured
                CacheLiveMediaFeaturedResponse::dispatch($locale);
            }
            // cache for home page
            CacheLiveMediaResponse::dispatch($locale);

            return response()->json([
                'message' => Lang::get('crud.update'),
            ]);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Delete (
     *     path="/{locale}/live-media/{id}",
     *     summary="Delete live media",
     *     description="Delete live media",
     *     operationId="liveMediaDelete",
     *     tags={"LiveMedia"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/id"
     * ),
     *     @OA\Response (
     *     response="200",
     *     ref="#/components/responses/200"
     * ),
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
     *     response="404",
     *     ref="#/components/responses/404"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param LiveMedia $live_medium
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, LiveMedia $live_medium): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('delete media');

        // begin database transaction
        DB::beginTransaction();
        try {
            $live_medium->delete();

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            // rollback changes
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }
}
