<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\MenuPublicResource;
use App\Http\Resources\Common\MenuResource;
use App\Models\Menu;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use OpenApi\Annotations as OA;
use Throwable;

class MenuController extends Controller
{

    /**
     * @OA\Get(
     *     path="/menu",
     *     summary="Get admin panel menu.",
     *     description="Get admin panel sidebar menu.",
     *     operationId="menuIndex",
     *     tags={"Menu"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/MenuResponse"
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
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny menu');

        $menus = Menu::with(['roles', 'children' => function ($q) {
            $q->with('roles')->orderBy('ordering');
        }])->whereNull('parent_id')
            ->orderBy('ordering')
            ->get();

        return MenuResource::collection($menus);
    }


    /**
     * @OA\Post(
     *     path="/menu",
     *     summary="Create admin panel menu.",
     *     description="Create admin panel sidebar menu.",
     *     operationId="menuStore",
     *     tags={"Menu"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *     required=true,
     *     description="Please enter valid information",
     *      @OA\JsonContent(
     *      required={"title", "roles"},
     *      @OA\Property(property="title", type="string", format="string", example="Users"),
     *      @OA\Property(property="link", type="string", format="string", example="/user/users", description="Required to create a child menu"),
     *      @OA\Property(property="icon", type="string", format="string", example="mdi-users"),
     *      @OA\Property(property="parent_id", type="integer", format="int64", example="1", description="Required to create a child menu"),
     *      @OA\Property(property="status", type="boolean", example=true, description="check, if menu is active or inactive"),
     *      @OA\Property(
     *     property="roles",
     *     type="array",
     *     @OA\Items(
     *     type="integer",
     *     format="int64",
     *     example="1",
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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException|Exception
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create menu');

        $this->validate($request, [
            'title' => 'required|min:3',
            'roles' => 'required'
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            // create menu
            $menu = new Menu();
            $menu->fill($request->all());
            $menu->save();

            $menu->roles()->attach($request->input('roles'));

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create')
            ], 201);
        } catch (Throwable  $exception) {
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
     * @OA\Patch(
     *     path="/menu/{menu}",
     *     summary="Update admin panel menu.",
     *     description="Update admin panel sidebar menu.",
     *     operationId="menuUpdate",
     *     tags={"Menu"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *     required=true,
     *     description="Please enter valid information",
     *      @OA\JsonContent(
     *      required={"title", "roles"},
     *      @OA\Property(property="title", type="string", format="string", example="Users"),
     *      @OA\Property(property="link", type="string", format="string", example="/user/users", description="Required to create a child menu"),
     *      @OA\Property(property="icon", type="string", format="string", example="mdi-users"),
     *      @OA\Property(property="parent_id", type="integer", format="int64", example="1", description="Required to create a child menu"),
     *      @OA\Property(property="status", type="boolean", example=true, description="check, if menu is active or inactive"),
     *     @OA\Property(
     *     property="roles",
     *     type="array",
     *     @OA\Items(
     *     type="integer",
     *     format="int64",
     *     example="1",
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
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Menu $menu
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, Menu $menu): JsonResponse
    {
        $this->authorize('update menu');

        $this->validate($request, [
            'title' => 'required|min:3'
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            // update menu
            $roles = $request->get('roles');
            if ($request->has('parent_id')) {
                $child = new Menu();
                $child->fill($request->except('children'));
                $child->save();

                $child->roles()->sync($roles);
                $menu->roles()->syncWithoutDetaching($roles);
                // commit database
                DB::commit();
                // return success message
                return response()->json([
                    'message' => Lang::get('crud.create')
                ]);
            } else {
                $menu->update($request->except('children'));
                $menu->roles()->sync($roles);
                if ($request->filled('link')) {
                    $menu->load('parent');
                    $menu->parent->roles()->syncWithoutDetaching($roles);
                }
                // commit database
                DB::commit();
                // return success message
                return response()->json([
                    'message' => Lang::get('crud.update')
                ]);
            }
        } catch (Throwable  $exception) {
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
     *  path="/menu/{menu}",
     *     summary="Delete admin panel menu",
     *  description="Delete admin panel menu",
     *  operationId="menuDelete",
     *  tags={"Menu"},
     *  security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *     description="Menu ID",
     *     in="path",
     *     name="menu",
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
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(
     *     @OA\Property(property="message", type="string", example="Not Found."),
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
     * Remove the specified resource from storage.
     *
     * @param Menu $menu
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Menu $menu): JsonResponse
    {
        $this->authorize('delete menu');

        try {
            // delete menu
            $menu->children()->delete();
            $menu->delete();

            return response()->json([
                'message' => Lang::get('crud.delete')
            ]);
        } catch (Throwable  $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post (
     *     path="/menu-reorder",
     *     summary="Reorder admin panel menu.",
     *     description="Reorder admin panel sidebar menu.",
     *     operationId="menuReorder",
     *     tags={"Menu"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *     required=true,
     *     description="Please enter valid information",
     *      @OA\JsonContent(
     *      required={"menus"},
     *      @OA\Property(
     *     property="menus",
     *     type="array",
     *     @OA\Items(
     *     type="integer",
     *     format="int64",
     *     example="1",
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
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function reorderMenu(Request $request): JsonResponse
    {
        $this->authorize('update menu');

        // begin database transaction
        DB::beginTransaction();
        try {
            // rearrange menu
            collect($request->input('menus'))->each(function ($menu, $key) {
                Menu::query()->where('id', '=', $menu)
                    ->update([
                        'ordering' => $key
                    ]);
            });

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update')
            ]);
        } catch (Throwable  $exception) {
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
     *     path="/menus",
     *     summary="Get sidebar menu assoicate to auth user.",
     *     description="Get admin panel menu assoicate to auth user.",
     *     operationId="menus",
     *     tags={"Menu"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(
     *     property="data",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/SidebarMenuResponse"
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
     * Get all menus by user permissions.
     *
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function getMenus(): JsonResponse|AnonymousResourceCollection
    {
        try {
            // get user role
            $role = collect(Auth::user()->roles)->first();
            $ids = DB::table('menu_role')
                ->where('role_id', '=', $role?->id)
                ->pluck('menu_id');
            // get menus
            $menus = $role->menus()->with(['children' => function ($child) use ($ids) {
                $child->where('status', '=', 1)
                    ->whereIn('id', $ids)
                    ->orderBy('ordering');
            }])->whereNull('parent_id')
                ->where('status', '=', 1)
                ->orderBy('ordering')
                ->get();

            // return success message
            return MenuPublicResource::collection($menus);
        } catch (Throwable  $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage(),
            ], 400);
        }
    }
}
