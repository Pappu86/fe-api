<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\TagEditResource;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Http\Resources\Post\TagResource;
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
use Throwable;

class TagController extends Controller
{
    use MetaImage;

    /**
     * @OA\Get (
     *     path="/{locale}/tag",
     *     summary="Get tag",
     *     description="Get tag list",
     *     operationId="tagIndex",
     *     tags={"Tag"},
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
     *     ref="#/components/schemas/Tag"
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

        $this->authorize('viewAny tag');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $tags = Tag::query()->withCount('posts')->latest();
        if ($query) {
            $tags = Tag::query()->whereTranslationLike('name', '%' . $query . '%');
        }
        if ($sortBy) {
            $tags = Tag::query()->withCount('posts')->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $tags->get();
            $tags = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $tags = $tags->paginate($per_page);
        }

        return TagResource::collection($tags);
    }

    /**
     * @OA\Get (
     *     path="/{locale}/tags",
     *     summary="Get tags",
     *     description="Get tag all",
     *     operationId="tagAll",
     *     tags={"Tag"},
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
     * Get all tags
     *
     * @param $locale
     * @return JsonResponse
     */
    public function getAll($locale): JsonResponse
    {
        $tags = DB::table('tags as c')
            ->join('tag_translations as ct', 'c.id', '=', 'ct.tag_id')
            ->select('c.id', 'ct.name')
            ->where('ct.locale', '=', $locale)
            ->get();

        return response()->json([
            'data' => $tags
        ]);
    }

    /**
     * @OA\Post (
     *     path="/{locale}/tag",
     *     summary="Store tag",
     *     description="Create tag",
     *     operationId="tagStore",
     *     tags={"Tag"},
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
     *              ref="#/components/schemas/Tag",
     *              required={"name"}
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="tagId", type="integer", example="1")
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

        $this->authorize('create tag');

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            $tag = Tag::query()->create($request->except('meta_image'));

            $this->saveMetaImage($request, $tag);

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'tagId' => $tag->id
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
     *     path="/{locale}/tag/{id}",
     *     summary="Get single tag",
     *     description="Get tag",
     *     operationId="tagShow",
     *     tags={"Tag"},
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
     *     ref="#/components/schemas/Tag"
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
     * Show tag.
     *
     * @param $locale
     * @param Tag $tag
     * @return TagEditResource
     */
    public function show($locale, Tag $tag): TagEditResource
    {
        App::setLocale($locale);

        return new TagEditResource($tag);
    }

    /**
     * @OA\Patch (
     *     path="/{locale}/tag/{id}",
     *     summary="Update tag",
     *     description="Update tag",
     *     operationId="tagUpdate",
     *     tags={"Tag"},
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
     *              ref="#/components/schemas/Tag",
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
     * @param Tag $tag
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, $locale, Tag $tag): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update tag');

        $translation = DB::table('tag_translations')
            ->where('tag_id', '=', $tag->id)
            ->where('locale', '=', $locale)
            ->first();
        $translation_id = $translation?->id;

        $this->validate($request, [
            'name' => 'required',
            'slug' => 'required|alpha_dash|unique:tag_translations,slug,' . $translation_id,
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN)
            ]);
            $tag->update($request->except('meta_image'));

            $this->saveMetaImage($request, $tag);
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
     *     path="/{locale}/tag/{id}",
     *     summary="Delete tag",
     *     description="Delete tag",
     *     operationId="tagDelete",
     *     tags={"Tag"},
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
     * @param Tag $tag
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Tag $tag): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('delete tag');

        // begin database transaction
        DB::beginTransaction();
        try {
            $tag->delete();

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
     *     path="/{locale}/tag-slug/{title}",
     *     summary="Check unique slug",
     *     description="Check unique slug for model",
     *     operationId="tagSlug",
     *     tags={"Tag"},
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
            $latest = TagTranslation::query()->where('slug', '=', $slug)
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

}
