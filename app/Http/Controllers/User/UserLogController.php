<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserLogResource;
use App\Models\UserLog;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use OpenApi\Annotations as OA;
use Throwable;

class UserLogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/user-logs",
     *     summary="Get user login logs",
     *     description="Get user login logs",
     *     operationId="userLogIndex",
     *     tags={"UserLog"},
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
     *     ref="#/components/parameters/page"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/per_page"
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/UserLogResponse"
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
     * Get all users.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('view user-log');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $logs = UserLog::query()->latest();
        if ($query) {
            $logs = UserLog::search($query);
        }
        if ($sortBy) {
            $logs = UserLog::query()->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $logs->get();
            $logs = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $logs = $logs->paginate($per_page);
        }
        return UserLogResource::collection($logs);
    }

    /**
     * @OA\Delete (
     *     path="/user-logs/{id}",
     *     summary="Delete user login logs",
     *     description="Delete user login logs",
     *     operationId="userLogDestroy",
     *     tags={"UserLog"},
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
     * Delete user-log log.
     *
     * @param UserLog $user_log
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(UserLog $user_log): JsonResponse
    {
        $this->authorize('delete user-log');
        DB::beginTransaction();
        try {
            // delete user-log log
            $user_log->delete();
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Delete (
     *     path="/user-logs",
     *     summary="Delete all user login logs",
     *     description="Delete all user login logs",
     *     operationId="userLogDestroyAll",
     *     tags={"UserLog"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Log ids",
     *     in="query",
     *     name="ids",
     *     example="1, 2, 3",
     *     @OA\Schema(
     *     type="string",
     * )
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
     * Delete all activities log.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $this->authorize('delete user-log');
        DB::beginTransaction();
        try {
            // delete all user-log log
            $ids = explode(',', $request->query('ids'));
            UserLog::query()
                ->whereIn('id', $ids)
                ->delete();

            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable $exception) {
            report($exception);
            DB::rollBack();
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage(),
            ], 400);
        }
    }
}
