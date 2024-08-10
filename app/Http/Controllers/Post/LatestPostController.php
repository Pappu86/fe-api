<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\LatestPostResource;
use App\Jobs\Cache\Post\CacheLatestPostResponse;
use App\Models\LatestPost;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Throwable;

class LatestPostController extends Controller
{

    /**
     * @OA\Get (
     *     path="/{locale}/latest-post",
     *     summary="Get Latest Post",
     *     description="Get Latest Post list",
     *     operationId="latestPostIndex",
     *     tags={"LatestPost"},
     *     security={ {"sanctum": {} }},
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
     *     ref="#/components/schemas/LatestPost"
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

        $this->authorize('viewAny latest-post');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $latest_posts = LatestPost::query()->latest();
        if ($query) {
            $latest_posts = LatestPost::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $latest_posts = LatestPost::query()->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $latest_posts->get();
            $latest_posts = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $latest_posts = $latest_posts->paginate($per_page);
        }

        return LatestPostResource::collection($latest_posts);
    }

    /**
     * @OA\Get (
     *     path="/latest-post-all",
     *     summary="Get all Latest Post",
     *     description="Get Latest Post list",
     *     operationId="latestPostAll",
     *     tags={"LatestPost"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/LatestPost"
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
    public function getAll(): AnonymousResourceCollection
    {
        $latest_post = LatestPost::query()->latest()->get();

        return LatestPostResource::collection($latest_post);
    }

    /**
     * @OA\Post (
     *     path="/{locale}/latest-post",
     *     summary="Store Latest Post",
     *     description="Create Latest Post",
     *     operationId="latestPostStore",
     *     tags={"LatestPost"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody (
     *     required=true,
     *     description="Please enter valid information",
     *     @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              ref="#/components/schemas/LatestPost",
     *              required={"title", "status"}
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
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

        $this->authorize('create latest-post');

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            $latest_post = LatestPost::query()->create($request->all());

            // commit changes
            DB::commit();

            // cache response
            CacheLatestPostResponse::dispatch($locale);

            return response()->json([
                'message' => Lang::get('crud.create'),
                'latestPostId' => $latest_post->id,
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
     *     path="/{locale}/latest-post/{id}",
     *     summary="Get single Latest Post",
     *     description="Get Latest Post",
     *     operationId="latestPostShow",
     *     tags={"LatestPost"},
     *     security={ {"sanctum": {} }},
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
     *     ref="#/components/schemas/LatestPost"
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
     * Show type.
     *
     * @param $locale
     * @param LatestPost $latest_post
     * @return LatestPostResource|JsonResponse
     */
    public function show($locale, LatestPost $latest_post): LatestPostResource|JsonResponse
    {
        App::setLocale($locale);

        try {
            return new LatestPostResource($latest_post);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * @OA\Patch (
     *     path="/{locale}/latest-post/{id}",
     *     summary="Update Latest Post",
     *     description="Update Latest Post",
     *     operationId="latestPostUpdate",
     *     tags={"LatestPost"},
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
     *          mediaType="application/json",
     *          @OA\Schema(
     *              ref="#/components/schemas/LatestPost",
     *              required={"title", "status"}
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
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $locale
     * @param LatestPost $latest_post
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, $locale, LatestPost $latest_post): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update latest-post');

        $this->validate($request, [
            'title' => 'required'
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            $latest_post->update($request->all());

            // commit changes
            DB::commit();

            // cache response
            CacheLatestPostResponse::dispatch($locale);

            return response()->json([
                'message' => Lang::get('crud.update')
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
     *     path="/{locale}/latest-post/{id}",
     *     summary="Delete Latest Post",
     *     description="Delete Latest Post",
     *     operationId="latestPostDelete",
     *     tags={"LatestPost"},
     *     security={ {"sanctum": {} }},
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
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param LatestPost $latest_post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, LatestPost $latest_post): JsonResponse
    {
        App::setLocale($locale);
        $this->authorize('delete latest-post');

        // begin database transaction
        DB::beginTransaction();
        try {
            $latest_post->delete();

            // commit changes
            DB::commit();

            // cache response
            CacheLatestPostResponse::dispatch($locale);

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
