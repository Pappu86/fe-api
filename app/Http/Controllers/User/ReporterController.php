<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Reporter;
use App\Http\Resources\User\ReporterResource;
use App\Traits\ProfileImage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Throwable;

class ReporterController extends Controller
{
    use ProfileImage;

    /**
     * @OA\Get (
     *     path="/{locale}/reporter",
     *     summary="Get reporter",
     *     description="Get reporter list",
     *     operationId="reporterIndex",
     *     tags={"Reporter"},
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
     *     ref="#/components/schemas/Reporter"
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

        $this->authorize('viewAny reporter');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $reporters = Reporter::query()->latest();
        if ($query) {
            $reporters = Reporter::query()->whereTranslationLike('name', '%' . $query . '%');
        }
        if ($sortBy) {
            $reporters = Reporter::query()->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $reporters->get();
            $reporters = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $reporters = $reporters->paginate($per_page);
        }

        return ReporterResource::collection($reporters);
    }

    /**
     * @OA\Get (
     *     path="/{locale}/reporters",
     *     summary="Get reporters",
     *     description="Get reporter all",
     *     operationId="reporterAll",
     *     tags={"Reporter"},
     *     security={ {"sanctum": {} }},
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
     *     ref="#/components/schemas/SelectResponse"
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
     * Get all reporters
     *
     * @param $locale
     * @return JsonResponse
     */
    public function getAll($locale): JsonResponse
    {
        $reporters = DB::table('reporters as c')
            ->join('reporter_translations as ct', 'c.id', '=', 'ct.reporter_id')
            ->select('c.id', 'ct.name')
            ->where('ct.locale', '=', $locale)
            ->get();

        return response()->json([
            'data' => $reporters
        ]);
    }

    /**
     * @OA\Post (
     *     path="/{locale}/reporter",
     *     summary="Store reporter",
     *     description="Create reporter",
     *     operationId="reporterStore",
     *     tags={"Reporter"},
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
     *              ref="#/components/schemas/Reporter",
     *              required={"name"}
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="reporterId", type="integer", example="1")
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

        $this->authorize('create reporter');

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            $reporter = Reporter::query()->create($request->all());

            $this->saveAvatar($request, $reporter);

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'reporterId' => $reporter->id
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
     *     path="/{locale}/reporter/{id}",
     *     summary="Get single reporter",
     *     description="Get reporter",
     *     operationId="reporterShow",
     *     tags={"Reporter"},
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
     *     ref="#/components/schemas/Reporter"
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
     * Show reporter.
     *
     * @param $locale
     * @param Reporter $reporter
     * @return ReporterResource
     */
    public function show($locale, Reporter $reporter): ReporterResource
    {
        App::setLocale($locale);

        return new ReporterResource($reporter);
    }

    /**
     * @OA\Patch (
     *     path="/{locale}/reporter/{id}",
     *     summary="Update reporter",
     *     description="Update reporter",
     *     operationId="reporterUpdate",
     *     tags={"Reporter"},
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
     *              ref="#/components/schemas/Reporter",
     *              required={"name"}
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
     * @param Reporter $reporter
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, $locale, Reporter $reporter): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update reporter');

        $this->validate($request, [
            'name' => 'required',
            'username' => 'required|alpha_dash|unique:reporters,username,' . $reporter->id,
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            $reporter->update($request->all());

            $this->saveAvatar($request, $reporter);

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
     *     path="/{locale}/reporter/{id}",
     *     summary="Delete reporter",
     *     description="Delete reporter",
     *     operationId="reporterDelete",
     *     tags={"Reporter"},
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
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Remove the specified resource from storage.
     *
     * @param $locale
     * @param Reporter $reporter
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Reporter $reporter): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('delete reporter');

        // begin database transaction
        DB::beginTransaction();
        try {
            $reporter->delete();

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

    /**
     * @OA\Get (
     *     path="/{locale}/reporter-username/{name}",
     *     summary="Check unique username",
     *     description="Check unique username for model",
     *     operationId="reporterSlug",
     *     tags={"Reporter"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Model name",
     *     in="path",
     *     name="name",
     *     required=true,
     *     @OA\Schema(
     *     type="string",
     * )
     * ),
     *     @OA\Response (
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(property="username", type="string", example="this-is-username"),
     *  ),
     * ),
     *     @OA\Response (
     *     response="401",
     *     ref="#/components/responses/401"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * Create username for model.
     *
     * @param $locale
     * @param $name
     * @return JsonResponse
     */
    public function checkUsername($locale, $name): JsonResponse
    {
        try {
            $username = Str::slug($name, '-', $locale);
            # username repeat check
            $latest = Reporter::query()->where('username', '=', $username)
                ->latest('id')
                ->value('username');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $username .= '-' . ($number + 1);
            }

            return response()->json([
                'username' => $username
            ]);

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'username' => null,
            ]);
        }
    }

}
