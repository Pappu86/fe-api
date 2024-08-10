<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\CategoryEditResource;
use App\Http\Resources\Post\CategoryTreeResource;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Http\Resources\Post\CategoryResource;
use App\Traits\MetaImage;
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
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Throwable;

class CategoryController extends Controller
{
    use MetaImage;

    /**
     * @OA\Get (
     *     path="/{locale}/category",
     *     summary="Get category",
     *     description="Get category list",
     *     operationId="categoryIndex",
     *     tags={"Category"},
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
     *     ref="#/components/schemas/CategoryResponse"
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
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function index($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $this->authorize('viewAny category');

        $categories = Category::with(['translations', 'children' => function ($query) {
            $query->with('translations')->withCount('posts');
        }])
            ->withCount('posts')
            ->whereNull('parent_id')
//            ->latest()
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * @OA\Get (
     *     path="/{locale}/categories",
     *     summary="Get categories",
     *     description="Get category all",
     *     operationId="categoryAll",
     *     tags={"Category"},
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
     * Get all categories
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     */
    public function getAll(Request $request, $locale): JsonResponse
    {
        $categories = DB::table('categories as c')
            ->join('category_translations as ct', 'c.id', '=', 'ct.category_id')
            ->select('c.id', 'ct.name')
            ->whereNull('c.parent_id')
            ->where('c.id', '!=', $request->query('category'))
            ->where('ct.locale', '=', $locale)
            ->get();

        return response()->json([
            'data' => $categories
        ]);
    }

    /**
     * @OA\Post (
     *     path="/{locale}/category",
     *     summary="Store category",
     *     description="Create category",
     *     operationId="categoryStore",
     *     tags={"Category"},
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
     *              ref="#/components/schemas/Category",
     *              required={"name"}
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="categoryId", type="integer", example="1")
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

        $this->authorize('create category');

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            $category = new Category();
            $category->fill($request->except('meta_image'));
            $category->save();

            $this->saveMetaImage($request, $category);

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'categoryId' => $category->id
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
     *     path="/{locale}/category/{id}",
     *     summary="Get single category",
     *     description="Get category",
     *     operationId="categoryShow",
     *     tags={"Category"},
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
     *     ref="#/components/schemas/Category"
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
     * Show category.
     *
     * @param $locale
     * @param Category $category
     * @return CategoryEditResource
     */
    public function show($locale, Category $category): CategoryEditResource
    {
        App::setLocale($locale);

        return new CategoryEditResource($category);
    }

    /**
     * @OA\Patch (
     *     path="/{locale}/category/{id}",
     *     summary="Update category",
     *     description="Update category",
     *     operationId="categoryUpdate",
     *     tags={"Category"},
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
     *              ref="#/components/schemas/Category",
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
     * @param Category $category
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, $locale, Category $category): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update category');

        $translation = DB::table('category_translations')
            ->where('category_id', '=', $category->id)
            ->where('locale', '=', $locale)
            ->first();
        $translation_id = $translation?->id;

        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|unique:category_translations,slug,' . $translation_id,
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            $category->update($request->except('meta_image'));

            $this->saveMetaImage($request, $category);

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
     *     path="/{locale}/category/{id}",
     *     summary="Delete category",
     *     description="Delete category",
     *     operationId="categoryDelete",
     *     tags={"Category"},
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
     * @param Category $category
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Category $category): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('delete category');

        // begin database transaction
        DB::beginTransaction();
        try {
            $category->children()->delete();
            $category->delete();

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
     *     path="/{locale}/category-slug/{title}",
     *     summary="Check unique slug",
     *     description="Check unique slug for model",
     *     operationId="categorySlug",
     *     tags={"Category"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Model title",
     *     in="path",
     *     name="title",
     *     required=true,
     *     @OA\Schema(
     *     type="string",
     * )
     * ),
     *     @OA\Response (
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(
     *     @OA\Property(property="slug", type="string", example="this-is-slug"),
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
     * Create slug for model.
     *
     * @param $locale
     * @param $title
     * @return JsonResponse
     */
    public function checkSlug($locale, $title): JsonResponse
    {
        try {
            $slug = Str::slug($title, '-', $locale);
            # slug repeat check
            $latest = CategoryTranslation::query()->where('slug', '=', $slug)
                ->latest('id')
                ->value('slug');

            if ($latest) {
                $pieces = explode('-', $latest);
                $number = intval(end($pieces));
                $slug .= '-' . ($number + 1);
            }

            return response()->json([
                'slug' => $slug
            ]);

        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // return failed message
            return response()->json([
                'slug' => null
            ]);
        }
    }

    /**
     * @OA\Get (
     *     path="/{locale}/categories-tree",
     *     summary="Get categories as tree",
     *     description="Get category all as tree",
     *     operationId="categoryAllTree",
     *     tags={"Category"},
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
     *     ref="#/components/schemas/CategoryTreeResponse"
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
     * Get all categories as tree
     *
     * @param $locale
     * @return AnonymousResourceCollection
     */
    public function getAllAsTree($locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $categories = Category::with(['translations', 'children' => function ($query) {
            $query->with('translations')
                ->where('status', '=', 1);
//                ->orderBy('ordering');
        }])
            ->whereNull('parent_id')
            ->where('status', '=', 1)
//            ->orderBy('ordering')
            ->get();

        return CategoryTreeResource::collection($categories);
    }

    /**
     * @OA\Post (
     *     path="/{locale}/category-reorder",
     *     summary="Reorder categories.",
     *     description="Reorder categories.",
     *     operationId="categoryReorder",
     *     tags={"Category"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *     required=true,
     *     description="Please enter valid information",
     *      @OA\JsonContent(
     *      required={"categories"},
     *      @OA\Property(
     *     property="categories",
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
     *      ref="#/components/responses/200"
     *  ),
     * @OA\Response(
     *     response=401,
     *     ref="#/components/responses/401"
     *  ),
     * @OA\Response(
     *     response=403,
     *     ref="#/components/responses/403"
     *  ),
     * @OA\Response(
     *     response=400,
     *     ref="#/components/responses/400"
     *  ),
     * @OA\Response(
     *     response="5XX",
     *     ref="#/components/responses/500"
     *  ),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function reorderCategories(Request $request): JsonResponse
    {
        $this->authorize('update category');

        // begin database transaction
        DB::beginTransaction();
        try {
            // rearrange menu
            collect($request->input('categories'))->each(function ($menu, $key) {
                Category::query()->where('id', '=', $menu)
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
}
