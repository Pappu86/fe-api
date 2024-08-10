<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\RoleEditResource;
use App\Http\Resources\User\RoleResource;
use App\Models\Role;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Spatie\Permission\Models\Permission;
use Throwable;

class RoleController extends Controller
{

    /**
     * @OA\Get(
     *     path="/permission",
     *     summary="Get permissions as tree.",
     *     description="Get permissions as tree.",
     *     operationId="rolePermissionTree",
     *     tags={"Role"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(
     *     property="user",
     *     type="object",
     *     @OA\Property (property="1", type="string", format="string", example="view user")
     * )
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
     * )
     *
     * Get permissions as tree.
     *
     * @return JsonResponse
     */
    public function getPermissions(): JsonResponse
    {
        $tree = [];

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            list($action, $model) = explode(' ', $permission->name);


            if (!isset($tree[$model])) {
                $tree[$model] = [];
            }
            $tree[$model][$permission->id] = $action;
        }

        return response()->json($tree);
    }

    /**
     * @OA\Get(
     *     path="/role-all",
     *     summary="Get roles.",
     *     description="Get roles.",
     *     operationId="roleAll",
     *     tags={"Role"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     @OA\Property (property="id", type="integer", format="int64", example="1"),
     *     @OA\Property (property="name", type="string", format="string", example="editor"),
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
     * )
     *
     * Get roles.
     *
     * @return JsonResponse
     */
    public function getAllRoles(): JsonResponse
    {
        $roles = Role::query()->select('id', 'name')->get();
        return response()->json([
            'data' => $roles
        ]);
    }

    /**
     * @OA\Get(
     *     path="/role",
     *     description="Get role list",
     *     operationId="roleIndex",
     *     tags={"Role"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Sort by",
     *     in="query",
     *     name="sortBy",
     *     example="name",
     *     @OA\Schema(
     *     type="string",
     * )
     * ),
     *     @OA\Parameter(
     *     description="Sort direction",
     *     in="query",
     *     name="direction",
     *     example="asc",
     *     @OA\Schema(
     *     type="string",
     *     enum={"asc", "desc"}
     * )
     * ),
     *     @OA\Parameter(
     *     description="Items per page",
     *     in="query",
     *     name="per_page",
     *     example="10",
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/Role"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
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
        $this->authorize('viewAny role');

        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $roles = Role::query()->withCount('users')->latest();

        if ($sortBy) {
            $roles = Role::query()->withCount('users')->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $roles->get();
            $roles = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $roles = $roles->paginate($per_page);
        }
        return RoleResource::collection($roles);
    }

    /**
     * @OA\Post(
     *  path="/role",
     *  description="Create new role",
     *  operationId="roleStore",
     *  tags={"Role"},
     *  security={ {"sanctum": {} }},
     *  @OA\RequestBody(
     *  required=true,
     *  description="Please enter valid information",
     *  @OA\JsonContent(
     *  required={"name"},
     *  @OA\Property(property="name", type="string", format="string", example="editor"),
     *  @OA\Property(
     *     property="permissions",
     *     type="array",
     *     @OA\Items(
     *     type="string",
     *     format="string",
     *     example="view user",
     * )
     * ),
     *  ),
     *  ),
     *      @OA\Response(
     *      response=201,
     *      description="Success",
     *      @OA\JsonContent(
     *      @OA\Property(property="message", type="string", example="Success"),
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
     * )
     *
     * Store new user into database.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create role');

        // validate request
        $data = $this->validate($request, [
            'name' => 'required|unique:roles',
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $data['guard_name'] = 'web';
            // create role
            $role = new Role();
            $role->fill($data);
            $role->save();

            // check, if request has permissions
            if ($request->filled('permissions')) {
                // assign permissions to role
                $role->syncPermissions($request->input('permissions'));
            }
            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create')
            ], 201);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/role/{role}",
     *     summary="Get role for edit",
     *     description="Get role",
     *     operationId="roleEdit",
     *     tags={"Role"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Role ID",
     *     in="path",
     *     name="role",
     *     example="1",
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/RoleEditResponse"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
     * )
     *
     * Edit role.
     *
     * @param Role $role
     * @return RoleEditResource
     * @throws AuthorizationException
     */
    public function show(Role $role): RoleEditResource
    {
        $this->authorize('update role');

        return RoleEditResource::make($role);
    }

    /**
     * @OA\Patch (
     *  path="/role/{role}",
     *     summary="Update role",
     *  description="Update role",
     *  operationId="roleUpdate",
     *  tags={"Role"},
     *  security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Role ID",
     *     in="path",
     *     name="role",
     *     example="1",
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     * )
     * ),
     *  @OA\RequestBody(
     *  required=true,
     *  description="Please enter valid information",
     *  @OA\JsonContent(
     *  required={"name"},
     *  @OA\Property(property="name", type="string", format="string", example="editor"),
     *  @OA\Property(
     *     property="permissions",
     *     type="array",
     *     @OA\Items(
     *     type="string",
     *     format="string",
     *     example="view user",
     * )
     * ),
     *  ),
     *  ),
     *      @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(
     *      @OA\Property(property="message", type="string", example="Success"),
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
     * )
     *
     * Update resource into database.
     *
     * @param Request $request
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $this->authorize('update role');

        // validate request
        $data = $this->validate($request, [
            'name' => 'required|unique:roles,name,' . $role->id,
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            // update role
            $role->update($data);
            // check, if request has permissions
            if ($request->filled('permissions')) {
                // assign permissions to role
                $role->syncPermissions($request->input('permissions'));
            }
            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Delete (
     *  path="/role/{role}",
     *     summary="Delete role",
     *  description="Delete role",
     *  operationId="roleDelete",
     *  tags={"Role"},
     *  security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Role ID",
     *     in="path",
     *     name="role",
     *     example="1",
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     * )
     * ),
     *      @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(
     *      @OA\Property(property="message", type="string", example="Success"),
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
     * )
     *
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete role');

        try {
            // delete role
            $role->delete();
            // delete permissions associated with this role.
            $role->syncPermissions([]);

            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Patch (
     *  path="/add-permission/{role}",
     *     summary="Add permissions to associate role",
     *  description="Add permissions to associate role",
     *  operationId="roleUpdatePermission",
     *  tags={"Role"},
     *  security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Role ID",
     *     in="path",
     *     name="role",
     *     example="1",
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     * )
     * ),
     *  @OA\RequestBody(
     *  required=true,
     *  description="Please enter valid information",
     *  @OA\JsonContent(
     *  required={"permissions"},
     *  @OA\Property(
     *     property="permissions",
     *     type="array",
     *     @OA\Items(
     *     type="string",
     *     format="string",
     *     example="view user",
     * )
     * ),
     *  ),
     *  ),
     *      @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\JsonContent(
     *      @OA\Property(property="message", type="string", example="Success"),
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
     * )
     *
     * Add permission to associate role.
     *
     * @param Request $request
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function addPermissions(Request $request, Role $role): JsonResponse
    {
        $this->authorize('update role');

        // validate request
        $this->validate($request, [
            'permissions' => 'required',
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            // assign permissions to role
            $role->syncPermissions($request->input('permissions'));
            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/permission/{role}",
     *     summary="Get permissions by role.",
     *     description="Get permissions by role.",
     *     operationId="rolePermission",
     *     tags={"Role"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Role ID",
     *     in="path",
     *     name="role",
     *     example="1",
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     * )
     * ),
     *     @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     type="integer",
     *     format="int64"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=403,
     *     description="Forbidden",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Internal Server Error."),
     *  )
     *  ),
     * )
     *
     * Get permissions by role.
     *
     * @param $role
     * @return JsonResponse
     */
    public function getPermissionsByRole($role): JsonResponse
    {
        $permissions = DB::table('role_has_permissions')
            ->where('role_id', '=', $role)
            ->pluck('permission_id');

        return response()->json([
            'data' => $permissions
        ]);
    }
}
