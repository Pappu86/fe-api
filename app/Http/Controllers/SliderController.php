<?php

namespace App\Http\Controllers;

use App\Http\Resources\SliderEditResource;
use App\Http\Resources\SliderResource;
use App\Jobs\Cache\CacheSliderResponse;
use App\Models\Slider;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class SliderController extends Controller
{
    /**
     * @OA\Get (
     *     path="/{locale}/slider",
     *     summary="Get slider",
     *     description="Get slider list",
     *     operationId="sliderIndex",
     *     tags={"Slider"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/query"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/page"
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
     *     ref="#/components/schemas/Slider"
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

        $this->authorize('viewAny slider');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        // $sliders = Slider::query()->orderBy('ordering')->latest();
        $sliders = Slider::with([
            'translations',
        ])
            ->whereHas('translations', function (Builder $q) use ($locale) {
                $q->where('locale', '=', $locale);
            })
            ->orderBy('ordering')->latest();        

        if ($query) {
            $sliders = Slider::whereTranslationLike('title', '%' . $query . '%');
        }
        if ($sortBy) {
            $sliders = Slider::query()->orderBy($sortBy, $direction);
        }
        $sliders = $sliders->get();

        // if ($per_page === '-1') {
        //     $results = $sliders->get();
        //     $sliders = new LengthAwarePaginator($results, $results->count(), -1);
        // } else {
        //     $sliders = $sliders->paginate($per_page);
        // }
        return SliderResource::collection($sliders);
    }

    /**
     * @OA\Post (
     *     path="/{locale}/slider",
     *     summary="Store slider",
     *     description="Create slider",
     *     operationId="sliderStore",
     *     tags={"Slider"},
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
     *              ref="#/components/schemas/Slider",
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="sliderId", type="integer", example="1")
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

        $this->authorize('create slider');

        $request->validate([
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'type' => 'required_unless:content,null',
            'image' => 'nullable|image',
            'video' => 'nullable|mimes:video/mp4,video/webm,video/ogg,video/mpeg,video/3gpp,video/3gpp2'
        ]);
        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                    'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                    'is_external' => filter_var($request->input('is_external'), FILTER_VALIDATE_BOOLEAN)
                ]
            );

            $slider = new Slider();
            $slider->fill($request->except('image', 'video'));
            $slider->save();

            $this->saveImage($request, $slider);

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'sliderId' => $slider->id
            ], 201);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * @OA\Get (
     *     path="/{locale}/slider/{id}",
     *     summary="Get single slider",
     *     description="Get slider",
     *     operationId="sliderShow",
     *     tags={"Slider"},
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
     *     ref="#/components/schemas/Slider"
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
     * Display the specified resource.
     *
     * @param $locale
     * @param Slider $slider
     * @return SliderEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Slider $slider): SliderEditResource|JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('view slider');

        try {
            return new SliderEditResource($slider);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * @OA\Patch (
     *     path="/{locale}/slider/{id}",
     *     summary="Update slider",
     *     description="Update slider",
     *     operationId="sliderUpdate",
     *     tags={"Slider"},
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
     *              ref="#/components/schemas/Slider",
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
     *     response="404",
     *     ref="#/components/responses/404"
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
     * @param Slider $slider
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, $locale, Slider $slider): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('update slider');

        $request->validate([
            'title' => 'required|string',
            'content' => 'nullable|string',
            'type' => 'required_unless:content,null',
            'image' => 'nullable|image',
            'video' => 'nullable|mimes:video/mp4,video/webm,video/ogg,video/mpeg,video/3gpp,video/3gpp2'
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $maxOrdaring=Slider::max('ordering');
            $ordaring=1;
            
            if($maxOrdaring){
                $ordaring=($maxOrdaring+1)*1;
            }
            $request->merge([
                    'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                    'is_external' => filter_var($request->input('is_external'), FILTER_VALIDATE_BOOLEAN),
                    'ordering' => $ordaring
                ]
            );

            $slider->update($request->except('image', 'video'));

            $this->saveImage($request, $slider);

            // commit database
            DB::commit();
            // cache response
            CacheSliderResponse::dispatch($locale);

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
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function reorderSlider(Request $request, $locale): JsonResponse
    {
        $this->authorize('update slider');

        DB::beginTransaction();
        try {
            // rearrange slider
            collect($request->input('sliders'))->each(function ($slider, $key) {
                Slider::query()->where('id', '=', $slider['id'])
                    ->update([
                        'ordering' => $key
                    ]);
            });

            // commit database
            DB::commit();
            // cache response
            CacheSliderResponse::dispatch($locale);
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
     * @OA\Delete (
     *     path="/{locale}/slider/{id}",
     *     summary="Delete slider",
     *     description="Delete slider",
     *     operationId="sliderDelete",
     *     tags={"Slider"},
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
     *     response="404",
     *     ref="#/components/responses/404"
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
     * @param Slider $slider
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Slider $slider): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('delete slider');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete slider
            $slider->delete();

            // commit database
            DB::commit();
            // cache response
            CacheSliderResponse::dispatch($locale);

            // return success message
            return response()->json([
                'message' => Lang::get('crud.trash')
            ]);
        } catch (Throwable $exception) {
            // log exception
            report($exception);
            // rollback database
            DB::rollBack();
            // return failed message
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 400);
        }
    }

    /**
     * Save slider image.
     *
     * @param Request $request
     * @param Slider $slider
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    private function saveImage(Request $request, Slider $slider): void
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $name = Str::slug(pathinfo($file_name, PATHINFO_FILENAME));
            $extension = Str::lower($file_extension);
            $slider->addMedia($file)
                ->usingName($name)
                ->usingFileName($name . '.' . $extension)
                ->toMediaCollection('image');

            $slider->forceFill([
                'content' => $slider->getFirstMediaUrl('image'),
                'type' => 'image',
            ]);
            $slider->save();
        }
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $name = Str::slug(pathinfo($file_name, PATHINFO_FILENAME));
            $extension = Str::lower($file_extension);
            $slider->addMedia($file)
                ->usingName($name)
                ->usingFileName($name . '.' . $extension)
                ->toMediaCollection('video');

            $slider->forceFill([
                'content' => $slider->getFirstMediaUrl('video'),
                'type' => 'video',
            ]);
            $slider->save();
        }
    }
}
