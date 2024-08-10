<?php

namespace App\Http\Controllers\User;

use App\Http\Resources\User\UserLogResource;
use App\Models\User;
use App\Http\Resources\User\UserEditResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\PublisherResource;
use App\Http\Resources\User\UserSingleResource;
use App\Models\UserLog;
use App\Traits\ProfileImage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\Permission\Models\Role;
use Throwable;

class UserController extends Controller
{
    use ProfileImage;

    /**
     * @OA\Get(
     *     path="/user",
     *     description="Get user list",
     *     operationId="userIndex",
     *     tags={"User"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Search query",
     *     in="query",
     *     name="query",
     *     example="john",
     *     @OA\Schema(
     *     type="string",
     * )
     * ),
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
     *     ref="#/components/schemas/User"
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
        $this->authorize('view user');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $users = User::with('roles')->latest();
        if ($query) {
            $users = User::search($query);
        }
        if ($sortBy) {
            $users = User::with('roles')->orderBy($sortBy, $direction);
        }
        if (!auth()->user()->hasRole('super-admin')) {
            $users = $users->role(Role::whereNotIn('name', ['super-admin'])->get());
        }
        if ($per_page === '-1') {
            $results = $users->get();
            $users = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $users = $users->paginate($per_page);
        }
        return UserResource::collection($users);
    }

    /**
     * @OA\Post(
     *  path="/user",
     *  description="Create new user",
     *  operationId="userStore",
     *  tags={"User"},
     *  security={ {"sanctum": {} }},
     *  @OA\RequestBody(
     *  required=true,
     *  description="Please enter valid information",
     *  @OA\JsonContent(
     *  required={"name", "email", "password", "status", "role"},
     *  @OA\Property(property="name", type="string", format="string", example="John Doe"),
     *  @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *  @OA\Property(property="mobile", type="string", format="string", example="+8801000000000"),
     *  @OA\Property(property="status", type="boolean", example="true"),
     *  @OA\Property(property="password", type="string", format="password", minLength=8, example="PassWord12345"),
     *  @OA\Property(property="role", type="string", format="string", example="editor"),
     *  ),
     *  ),
     *  @OA\Response(
     *  response=201,
     *  description="Success",
     *  @OA\JsonContent(
     *  @OA\Property(property="message", type="string", example="Success"),
     *  ),
     *  ),
     *  @OA\Response(
     *  response=401,
     *  description="Unauthenticated",
     *  @OA\JsonContent(
     *  @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *  @OA\Response(
     *  response=400,
     *  description="Bad Request",
     *  @OA\JsonContent(
     *  @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
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
        $this->authorize('create user');

        // validate request
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
            'mobile' => 'nullable|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required'
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            // check if request has email verified option
            $emailVerified = filter_var($request->input('email_verified_at'), FILTER_VALIDATE_BOOLEAN);
            if ($emailVerified) {
                $request->merge([
                    'email_verified_at' => now()
                ]);
            }
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            if ($request->filled('password')) {
                $request->merge([
                    'password' => Hash::make($request->input('password')),
                ]);
            }
            // create user
            $user = new User();
            $user->fill($request->all());
            $user->save();

            $this->saveAvatar($request, $user);

            // assign role to user
            $user->assignRole($request->input('role'));
            // fire register event to send email verification link
            if (!$emailVerified) {
                event(new Registered($user));
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
     *     path="/user/{user}",
     *     description="Get user",
     *     operationId="userShow",
     *     tags={"User"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="User ID",
     *     in="path",
     *     name="user",
     *     required=true,
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
     *     @OA\Property(property="message", type="string", example="Success"),
     *     @OA\Property(property="data", type="object", ref="#/components/schemas/UserEditResponse")
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
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Not Found."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     * )
     *
     * Get single user.
     *
     * @param User $user
     * @return UserSingleResource
     */
    public function show(User $user): UserSingleResource
    {
        return UserSingleResource::make($user);
    }

    /**
     * @OA\Get(
     *     path="/user/{user}/edit",
     *     description="Get user",
     *     operationId="userEdit",
     *     tags={"User"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="User ID",
     *     in="path",
     *     name="user",
     *     required=true,
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
     *     @OA\Property(property="message", type="string", example="Success"),
     *     @OA\Property(property="data", type="object", ref="#/components/schemas/UserEditResponse")
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
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Not Found."),
     *  )
     *  ),
     *     @OA\Response(
     *     response=400,
     *     description="Bad Request",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     * )
     *
     * Edit user.
     *
     * @param User $user
     * @return UserEditResource
     * @throws AuthorizationException
     */
    public function edit(User $user): UserEditResource
    {
        $this->authorize('update user');

        return UserEditResource::make($user);
    }

    /**
     * @OA\Patch(
     *  path="/user/{user}",
     *  description="Update user",
     *  operationId="userUpdate",
     *  tags={"User"},
     *  security={ {"sanctum": {} }},
     *  @OA\Parameter(
     *  description="User ID",
     *  in="path",
     *  name="user",
     *  required=true,
     *  example="1",
     *  @OA\Schema(
     *  type="integer",
     *  format="int64"
     *  )
     *  ),
     *  @OA\RequestBody(
     *  required=true,
     *  description="Please enter valid information",
     *  @OA\JsonContent(
     *  required={"name", "email", "status", "role"},
     *  @OA\Property(property="name", type="string", format="string", example="John Doe"),
     *  @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *  @OA\Property(property="mobile", type="string", format="string", example="+8801000000000"),
     *  @OA\Property(property="status", type="boolean", example="true"),
     *  @OA\Property(property="password", type="string", format="password", minLength=8, example="PassWord12345"),
     *  @OA\Property(property="role", type="string", format="string", example="editor"),
     *  ),
     *  ),
     *  @OA\Response(
     *  response=200,
     *  description="Success",
     *  @OA\JsonContent(
     *  @OA\Property(property="message", type="string", example="Success"),
     *  ),
     *  ),
     *  @OA\Response(
     *  response=401,
     *  description="Unauthenticated",
     *  @OA\JsonContent(
     *  @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *  )
     *  ),
     *  @OA\Response(
     *  response=404,
     *  description="Not Found",
     *  @OA\JsonContent(
     *  @OA\Property(property="message", type="string", example="Not Found."),
     *  )
     *  ),
     *  @OA\Response(
     *  response=400,
     *  description="Bad Request",
     *  @OA\JsonContent(
     *  @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *  )
     *  ),
     * )
     *
     * Update record into database.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorize('update user');

        // validate request
        $this->validate($request, [
            'name' => 'required',
            'mobile' => 'nullable|unique:users,mobile,' . $user->id,
            'status' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'role' => 'required'
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            // check if request has email verified option
            $emailVerified = filter_var($request->input('email_verified_at'), FILTER_VALIDATE_BOOLEAN);
            if ($emailVerified) {
                $request->merge([
                    'email_verified_at' => now()
                ]);
            }
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
            ]);
            if ($request->filled('password')) {
                $request->merge([
                    'password' => Hash::make($request->input('password')),
                ]);
            }
            // update user
            $user->update($request->all());

            $this->saveAvatar($request, $user);
            // sync role to user
            $user->syncRoles($request->input('role'));
            // fire register event to send email verification link
            if (!$emailVerified) {
                event(new Registered($user));
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
     * @OA\Delete(
     * path="/user/{user}",
     * description="Delete user",
     * operationId="userDelete",
     * tags={"User"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="User ID",
     *    in="path",
     *    name="user",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *     ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *     )
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Not Found",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not Found."),
     *     )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * )
     * Delete user from database.
     *
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException | Throwable
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete user');
        // begin database transaction
        DB::beginTransaction();
        try {
            // delete user
            $user->delete();
            // delete roles associated with this user.
            $user->syncRoles([]);
            // commit changes
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback changes
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     * path="/update-user-info/{user}",
     * description="Update user information",
     * operationId="userUpdateInfo",
     * tags={"User"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="User ID",
     *    in="path",
     *    name="user",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Please enter valid information",
     *    @OA\JsonContent(
     *       required={"name", "email"},
     *       @OA\Property(property="name", type="string", format="string", example="John Doe"),
     *       @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *       @OA\Property(property="mobile", type="string", format="string", example="+8801000000000"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="user", type="object", ref="#/components/schemas/UserSingleResponse")
     *     ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *     )
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Not Found",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not Found."),
     *     )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * )
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException | Throwable
     */
    public function updateUserInfo(Request $request, User $user): JsonResponse
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'mobile' => 'nullable|unique:users,mobile,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            // update user
            $user->update($data);
            // commit changes
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'user' => UserSingleResource::make($user)
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback changes
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     * path="/update-user-password/{user}",
     * description="Update user password",
     * operationId="userUpdatePassword",
     * tags={"User"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="User ID",
     *    in="path",
     *    name="user",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="Please enter valid information",
     *    @OA\JsonContent(
     *       required={"password", "password_confirmation"},
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="user", type="object", ref="#/components/schemas/UserSingleResponse")
     *     ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *     )
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Not Found",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not Found."),
     *     )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * )
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException | Throwable
     */
    public function updateUserPassword(Request $request, User $user): JsonResponse
    {
        $data = $this->validate($request, [
            'password' => 'required|min:8|confirmed'
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            $newPassword=$request->get('password');
            $data['password'] = Hash::make($newPassword);
            // update user
            $user->update($data);
            // commit changes
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'user' => UserSingleResource::make($user)
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback changes
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     * path="/update-user-avatar/{user}",
     * description="Update user avatar",
     * operationId="userUpdateAvatar",
     * tags={"User"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="User ID",
     *    in="path",
     *    name="user",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              @OA\Property(
     *                  description="Binary content of file",
     *                  property="avatar",
     *                  type="string",
     *                  format="binary",
     *              ),
     *              required={"avatar"}
     *          )
     *      )
     *  ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="user", type="object", ref="#/components/schemas/UserSingleResponse")
     *     ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unauthenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Please sign in to use this service.")
     *     )
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Not Found",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not Found."),
     *     )
     * ),
     * @OA\Response(
     *    response=400,
     *    description="Bad Request",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
     *     )
     * ),
     * )
     *
     * Upload user avatar.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    public function updateUserAvatar(Request $request, User $user): JsonResponse
    {
        $this->validate($request, [
            'image' => 'required|image'
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            $this->saveAvatar($request, $user);
            // commit changes
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
                'user' => UserSingleResource::make($user)
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback changes
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Get all users
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
     public function getAllUsers(): JsonResponse
     {
        $users = DB::table('users')->get();
        return response()->json([
            'data' =>PublisherResource::collection($users)
        ]);
    }
}