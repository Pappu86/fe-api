<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\TypeAllResource;
use App\Models\Type;
use App\Http\Resources\Post\TypeResource;
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

class TypeController extends Controller
{

    /**
     * @OA\Get (
     *     path="/type",
     *     summary="Get type",
     *     description="Get type list",
     *     operationId="typeIndex",
     *     tags={"Type"},
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
        $this->authorize('viewAny type');

        $types = Type::query()->latest()->get();

        return TypeResource::collection($types);
    }

    /**
     * @OA\Get (
     *     path="/type-all",
     *     summary="Get all type",
     *     description="Get type list",
     *     operationId="typeAll",
     *     tags={"Type"},
     *     security={ {"sanctum": {} }},
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
     * @return AnonymousResourceCollection
     */
    public function getAll(): AnonymousResourceCollection
    {
        $types = Type::query()->latest()->get();

        return TypeAllResource::collection($types);
    }

    /**
     * @OA\Post (
     *     path="/type",
     *     summary="Store type",
     *     description="Create type",
     *     operationId="typeStore",
     *     tags={"Type"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody (
     *     required=true,
     *     description="Please enter valid information",
     *     @OA\JsonContent(
     *       @OA\Property(property="key", type="string", example="top-left"),
     *       @OA\Property(property="label", type="string", example="Top Left"),
     *       required={"key", "label"}
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
        $this->authorize('create type');

        // begin database transaction
        DB::beginTransaction();
        try {
            $type = Type::query()->create($request->all());

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'typeId' => $type->id,
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
     *     path="/type/{id}",
     *     summary="Get single type",
     *     description="Get type",
     *     operationId="typeShow",
     *     tags={"Type"},
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
     * @param Type $type
     * @return TypeResource
     */
    public function show(Type $type): TypeResource
    {
        return new TypeResource($type);
    }

    /**
     * @OA\Patch (
     *     path="/type/{id}",
     *     summary="Update type",
     *     description="Update type",
     *     operationId="typeUpdate",
     *     tags={"Type"},
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
     *       @OA\Property(property="key", type="string", example="top-left"),
     *       @OA\Property(property="label", type="string", example="Top Left"),
     *       required={"key", "label"}
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
     * @param Type $type
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, Type $type): JsonResponse
    {
        $this->authorize('update type');

        $this->validate($request, [
            'label' => 'required',
            'key' => 'required|alpha_dash|unique:types,key,' . $type->id,
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $type->update($request->all());

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
     *     path="/type/{id}",
     *     summary="Delete type",
     *     description="Delete type",
     *     operationId="typeDelete",
     *     tags={"Type"},
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
     * @param Type $type
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Type $type): JsonResponse
    {
        $this->authorize('delete type');

        // begin database transaction
        DB::beginTransaction();
        try {
            $type->delete();

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
