<?php

namespace App\Http\Controllers\Advertisement;

use App\Http\Controllers\Controller;
use App\Http\Resources\Advertisement\AdvertisementEditResource;
use App\Http\Resources\Advertisement\AdvertisementResource;
use App\Jobs\Media\SaveAdvertisementFile;
use App\Models\Advertisement;
use App\Models\AdvertisementImage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdvertisementController extends Controller
{

    /**
     * @OA\Get (
     *     path="/ads/advertisement",
     *     summary="Get advertisement",
     *     description="Get advertisement with paginate",
     *     operationId="adsIndex",
     *     tags={"Advertisement"},
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
     *     ref="#/components/schemas/Advertisement"
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
        $this->authorize('viewAny advertisement');
        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $ads = Advertisement::query()->latest();
        if ($query) {
            $ads = $ads->where('title', 'like', '%' . $request->get('query') . '%');
        }
        if ($sortBy) {
            $ads = $ads->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $ads->get();
            $ads = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $ads = $ads->paginate($per_page);
        }

        return AdvertisementResource::collection($ads);
    }

    /**
     * @OA\Post (
     *     path="/ads/advertisement",
     *     summary="Store advertisement",
     *     description="Store advertisement",
     *     operationId="adsStore",
     *     tags={"Advertisement"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody (
     *     required=true,
     *     description="Please enter valid information",
     *     @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              ref="#/components/schemas/Advertisement"
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="adsId", type="integer", example="1")
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
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create advertisement');

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'is_modal' => filter_var($request->input('is_modal'), FILTER_VALIDATE_BOOLEAN),
                'is_external' => filter_var($request->input('is_external'), FILTER_VALIDATE_BOOLEAN),
                'is_auto_modal' => filter_var($request->input('is_auto_modal'), FILTER_VALIDATE_BOOLEAN),
                'has_mobile_content' => filter_var($request->input('has_mobile_content'), FILTER_VALIDATE_BOOLEAN),
            ]);
            $ads = Advertisement::query()->create($request->except('image', 'video', 'document'));

            $this->saveFile($request, $ads);

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.create'),
                'advertisementId' => $ads->id
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
     *     path="/ads/advertisement/{id}",
     *     summary="Get advertisement",
     *     description="Get advertisement",
     *     operationId="adsShow",
     *     tags={"Advertisement"},
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
     *     ref="#/components/schemas/Advertisement"
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
     * @param Advertisement $advertisement
     * @return AdvertisementEditResource
     */
    public function show(Advertisement $advertisement): AdvertisementEditResource
    {
        return AdvertisementEditResource::make($advertisement);
    }

    /**
     * @OA\Patch (
     *     path="/ads/advertisement/{id}",
     *     summary="Update advertisement",
     *     description="Update advertisement",
     *     operationId="adsUpdate",
     *     tags={"Advertisement"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter (
     *     ref="#/components/parameters/id"
     * ),
     *     @OA\RequestBody (
     *     required=true,
     *     description="Please enter valid information",
     *     @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              ref="#/components/schemas/Advertisement"
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
     * @param Advertisement $advertisement
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Advertisement $advertisement): JsonResponse
    {
        $this->authorize('update advertisement');

        $request->validate([
            'provider_id' => 'required',
            'position_id' => 'required|unique:advertisements,position_id,' . $advertisement?->id,
            'title' => 'required|string',
            'type' => 'required|in:image,iframe,video,document',
            'image' => 'nullable|image',            
            'video' => 'nullable|mimes:video/mp4,video/webm,video/ogg,video/mpeg,video/3gpp,video/3gpp2',
            'content' => 'nullable|string',
            'document' => 'nullable|mimes:application/pdf',
        ]);

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'is_modal' => filter_var($request->input('is_modal'), FILTER_VALIDATE_BOOLEAN),
                'is_external' => filter_var($request->input('is_external'), FILTER_VALIDATE_BOOLEAN),
                'is_auto_modal' => filter_var($request->input('is_auto_modal'), FILTER_VALIDATE_BOOLEAN),
                'has_mobile_content' => filter_var($request->input('has_mobile_content'), FILTER_VALIDATE_BOOLEAN),
            ]);
            $advertisement->update($request->except('image', 'video', 'document'));
            $this->saveFile($request, $advertisement);

            // For multi ads implementation
            $multiImages=$request->input('images');             
            $isMultiAds=$request->input('is_multi_ads');
            $advertisementId=$advertisement->id;
            if($isMultiAds && $advertisementId){
                $existImages=DB::table('advertisement_images')->where('advertisement_id', '=', $advertisementId)->get();
                $totalExistImage=$existImages->count();
                $totalMultiImage=0;
                if(!empty($multiImages)){
                    $totalMultiImage=count($multiImages);
                }                
                if($totalExistImage>$totalMultiImage){
                    // remove aditional ads
                    foreach ($existImages as $image) {                      
                        $imgSRC=$image->src;
                        if(empty($multiImages)){
                            DB::table('advertisement_images')->where('advertisement_id',$advertisementId)->where('src',$imgSRC)->delete();                            
                        }else{
                            $isExist = array_filter(
                                $multiImages,
                                function($obj)use ($imgSRC){ 
                                   return $obj['src'] === $imgSRC;
                                });
                            if(empty($isExist)){
                                DB::table('advertisement_images')->where('advertisement_id',$advertisementId)->where('src',$imgSRC)->delete();
                            }
                        }
                    } 
                }else{
                    // Add new aditional ads
                    if(!empty($multiImages)){
                        foreach ($multiImages as $image) { 
                            $existImage=DB::table('advertisement_images')->where('advertisement_id', '=', $advertisementId)->where('src', '=', $image['src'])->first();
                            if(!isset($existImage)){
                                AdvertisementImage::query()->create([
                                    'advertisement_id' => $image['advertisement_id'],
                                    'src' => $image['src'],
                                ]); 
                            }
                        }
                    }
                       
                }
            }           

            // commit changes
            DB::commit();
            return response()->json([
                'message' => Lang::get('crud.update'),
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
     *     path="/ads/advertisement/{id}",
     *     summary="Delete advertisement",
     *     description="Delete advertisement",
     *     operationId="adsDelete",
     *     tags={"Advertisement"},
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
     *     response="404",
     *     ref="#/components/responses/404"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     * Remove the specified resource from storage.
     *
     * @param Advertisement $advertisement
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Advertisement $advertisement): JsonResponse
    {
        $this->authorize('delete advertisement');

        // begin database transaction
        DB::beginTransaction();
        try {
            $advertisement->delete();

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
     * Save slider image.
     *
     * @param Request $request
     * @param Advertisement $advertisement
     */
    private function saveFile(Request $request, Advertisement $advertisement): void
    {
        if ($request->hasFile('image')) {
            $this->saveMedia($request, $advertisement, 'image', 'content');            
        }
        if ($request->hasFile('mobile_image')) {
            $this->saveMedia($request, $advertisement, 'mobile_image', 'mobile_content');
        }
        if ($request->hasFile('video')) {
            $this->saveMedia($request, $advertisement, 'video', 'content');
        }
        if ($request->hasFile('mobile_video')) {
            $this->saveMedia($request, $advertisement, 'mobile_video', 'mobile_content');
        }
        if ($request->hasFile('document')) {
            $this->saveMedia($request, $advertisement, 'document', 'content');
        }
        if ($request->hasFile('mobile_document')) {
            $this->saveMedia($request, $advertisement, 'mobile_document', 'mobile_content');
        }
    }

    /**
     * @param Request $request
     * @param Advertisement $advertisement
     * @param $key
     * @param $field
     */
    private function saveMedia(Request $request, Advertisement $advertisement, $key, $field)
    {
        $file = $request->file($key);
        $file_name = $file->getClientOriginalName();
        $file_extension = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file_name, PATHINFO_FILENAME));
        $extension = Str::lower($file_extension);
        try {
            $advertisement->addMedia($file)
                ->usingName($name)
                ->usingFileName($name . '.' . $extension)
                ->toMediaCollection($key);

            SaveAdvertisementFile::dispatch($advertisement, $key, $field);
        } catch (FileDoesNotExist|FileIsTooBig $e) {
            report($e);
        }
    }
}