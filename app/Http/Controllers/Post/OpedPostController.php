<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\OpedPost;
use App\Http\Resources\Post\OpedPostResource;
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

class OpedPostController extends Controller
{

    /**
     * @OA\Get (
     *     path="/opedpost",
     *     summary="Get opedpost",
     *     description="Get opedpost list",
     *     operationId="opedpostIndex",
     *     tags={"OpedPost"},
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
     *     ref="#/components/schemas/Type"
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
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // $this->authorize('viewAny type');
        $post_id = $request->query('post_id');

        $opedpost = OpedPost::query()->latest()->get();
        if ($post_id) {
            $opedpost = OpedPost::where('post_id', '=', $post_id)->get();
        }

        return OpedPostResource::collection($opedpost);
    }


    /**
     * @OA\Post (
     *     path="/opedpost",
     *     summary="Store opedpost",
     *     description="Create opedpost",
     *     operationId="opedpostStore",
     *     tags={"OpedPost"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody (
     *     required=true,
     *     description="Please enter valid information",
     *     @OA\JsonContent(
     *       @OA\Property(property="post_id", type="integer", example="1"),
     *       @OA\Property(property="category_id", type="integer", example="2"),
     *       required={"post_id", "category_id"}
     *     ),
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
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        // begin database transaction
        DB::beginTransaction();
        try {
            $opedpost = OpedPost::query()->create($request->all());

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'opedpostId' => $opedpost->id,
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
     *     path="/opedpost/{id}",
     *     summary="Get single opedpost",
     *     description="Get opedpost",
     *     operationId="opedpostShow",
     *     tags={"OpedPost"},
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
     *     ref="#/components/schemas/Type"
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
     * @param OpedPost $opedpost
     * @return OpedPostResource
     */
    public function show(OpedPost $opedpost): OpedPostResource
    {
        return new OpedPostResource($opedpost);
    }

    /**
     * @OA\Patch (
     *     path="/opedpost/{id}",
     *     summary="Update opedpost",
     *     description="Update opedpost",
     *     operationId="opedpostUpdate",
     *     tags={"OpedPost"},
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
     *     @OA\JsonContent(
     *       @OA\Property(property="post_id", type="integer", example="1"),
     *       @OA\Property(property="category_id", type="integer", example="2"),
     *       required={"post_id", "category_id"}
     *     ),
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
     * @param OpedPost $opedpost
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, OpedPost $opedpost): JsonResponse
    {

        $this->validate($request, [
            'post_id' => 'required',
            'category_id' => 'required',
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $opedpost->update($request->all());

            // commit changes
            DB::commit();
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
     *     path="/opedpost/{id}",
     *     summary="Delete opedpost",
     *     description="Delete opedpost",
     *     operationId="opedpostDelete",
     *     tags={"OpedPost"},
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
     * @param OpedPost $opedpost
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(OpedPost $opedpost): JsonResponse
    {

        // begin database transaction
        DB::beginTransaction();
        try {
            $opedpost->delete();

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
