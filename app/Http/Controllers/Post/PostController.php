<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Jobs\Cache\Post\CachePostColumnFourResponse;
use App\Jobs\Cache\Post\CachePostColumnOneResponse;
use App\Jobs\Cache\Post\CachePostColumnThreeResponse;
use App\Jobs\Cache\Post\CachePostColumnTwoResponse;
use App\Jobs\Search\DeletePostDocument;
use App\Jobs\Search\UpdatePostDocument;
use App\Jobs\CreateSliderFromPost;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Http\Requests\PostRequest;
use App\Http\Resources\Post\PostEditResource;
use App\Http\Resources\Post\PostResource;
use App\Models\PostUpdatedHistory;
use App\Traits\Image;
use App\Traits\MetaImage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use OpenApi\Annotations as OA;
use Throwable;

class PostController extends Controller
{
    use MetaImage, Image;

    /**
     * @OA\Get (
     *     path="/{locale}/post",
     *     summary="Get post",
     *     description="Get post list",
     *     operationId="postIndex",
     *     tags={"Post"},
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
     *     ref="#/components/schemas/Post"
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

        $this->authorize('viewAny post');

        $query = $request->query('query');
        $publisherId = $request->query('publisher');
        $reporterId = $request->query('reporter');
        $categoryId = $request->query('category');
        $dateRange = $request->query('date_range');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);
        
        $posts =Post::query()->latest();
        
        if ($query) {
            $posts = $posts->whereTranslationLike('title', '%' . $query . '%')
                ->orWhereTranslationLike('short_title', '%' . $query . '%')
                ->orWhereTranslationLike('slug', '%' . $query . '%');

            // $posts = Post::whereTranslationLike('title', '%' . $query . '%');
        }

        if ($request->has('publisher') && $publisherId) {
            $posts= $posts->where('user_id', '=', $publisherId);
        }

        if ($request->has('reporter') && $reporterId) {
            $posts= $posts->where('reporter_id', '=', $reporterId);
        }

        if ($request->has('category') && $categoryId) {
            $posts= $posts->where('category_id', '=', $categoryId);
        }

        if (isset($dateRange)) {
           $splitDateRange= explode(",",$dateRange);
           $startDate = Carbon::parse($splitDateRange[0])->startOfDay();
            $endDate = Carbon::parse($splitDateRange[1])->endOfDay();

            if($startDate && $endDate){
                $allWinner = $posts->whereBetween('created_at', [date($startDate), date($endDate)]);
            }  
        }

        if ($sortBy) {
            $posts = $posts->orderBy($sortBy, $direction);
        }
        if ($per_page === '-1') {
            $results = $posts->get();
            $posts = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $posts = $posts->paginate($per_page);
        }

        // Include history array
        foreach ($posts as $post) {
            $postId=$post->id;
            $editedHistory = DB::table('post_updated_histories')
            ->where('post_id', '=', $postId)
            ->get(); 
            
            if(sizeof($editedHistory)>0){
                $newHistory=array();
                $index=0; 

                foreach ($editedHistory as $history) {
                    $user=DB::table('users')->where('id', '=', $history->user_id)->first();
                    if($user){
                        $history->name= $user->name;
                    }
                    array_push($newHistory,$history);
                    $index++;
                }
                $post['history']=$newHistory;
            }
        }

        return PostResource::collection($posts);
    }

    /**
     * @OA\Post (
     *     path="/{locale}/post",
     *     summary="Store post",
     *     description="Create post",
     *     operationId="postStore",
     *     tags={"Post"},
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
     *              ref="#/components/schemas/Post",
     *          )
     *      )
     * ),
     *     @OA\Response (
     *     response="201",
     *     description="Success",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Success"),
     *       @OA\Property(property="postId", type="integer", example="1")
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

        $this->authorize('create post');

        // begin database transaction
        DB::beginTransaction();
        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'is_fb_article' => filter_var($request->input('is_fb_article'), FILTER_VALIDATE_BOOLEAN),
            ]);
            $request->merge([
                'user_id' => auth()->id(),
                'datetime' => $request->input('datetime') ?? now()
            ]);
            $post = new Post();
            $post->fill($request->except('image', 'meta_image'));
            $post->save();

            $this->saveImage($request, $post);
            $this->saveMetaImage($request, $post);

            // commit database
            DB::commit();

            // return success message
            return response()->json([
                'message' => Lang::get('crud.create'),
                'postId' => $post->id
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
     *     path="/{locale}/post/{id}",
     *     summary="Get single post",
     *     description="Get post",
     *     operationId="postShow",
     *     tags={"Post"},
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
     *     ref="#/components/schemas/Post"
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
     * @param Post $post
     * @return PostEditResource|JsonResponse
     * @throws AuthorizationException
     */
    public function show($locale, Post $post): PostEditResource|JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('view post');

        try {
            return new PostEditResource($post);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json([
                'message' => Lang::get('crud.error')
            ], 404);
        }
    }

    /**
     * @OA\Patch (
     *     path="/{locale}/post/{id}",
     *     summary="Update post",
     *     description="Update post",
     *     operationId="postUpdate",
     *     tags={"Post"},
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
     *              ref="#/components/schemas/Post",
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
     * @param PostRequest $request
     * @param $locale
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(PostRequest $request, $locale, Post $post): JsonResponse
    {

        App::setLocale($locale);

        $this->authorize('update post');

        // begin database transaction
        DB::beginTransaction();

        try {
            $request->merge([
                'status' => filter_var($request->input('status'), FILTER_VALIDATE_BOOLEAN),
                'is_fb_article' => filter_var($request->input('is_fb_article'), FILTER_VALIDATE_BOOLEAN),
            ]);

            // 6 hours +/- for adsust post datetime field
            // only for custom published date time change
            if($request->input('isCustomDatetime') =='true'){
                $timestamp = $request->input('datetime');
                $originalTime = Carbon::parse($timestamp);
                $timeFormate=Carbon::now()->format('a');
                if($timeFormate=='am'){
                    $newTime = $originalTime->subHours(6);
                }else{
                    $newTime = $originalTime->addHours(6);
                }                
                $request->merge([                
                'datetime' => $newTime ?? now()
                ]);
            }

            // Previous post
            $oldPost = Post::where('id', $post->id)->first();
            if($oldPost && $oldPost->reporter_id){
                $request->merge(['is_edited' => 1]);                
            }

            $isUpdate=$post->update($request->except('image', 'meta_image')); 
            
            // It will be execute first time updated news post
            // First time published datetime and updated datetime will be asme
            if($oldPost && !isset($oldPost->reporter_id)){
                Post::where('id', $post->id)->update([
                    'datetime' => $post->updated_at,
                ]);                
            }

            // create edit post history
            if($isUpdate && $oldPost->reporter_id){
                Post::where('id', $post->id)->update([
                    'updated_at' => now(),
                ]);
                $postUpdatedHistory = new PostUpdatedHistory();
                $postUpdatedHistory->fill([
                    'post_id' => $post->id,
                    'user_id' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $postUpdatedHistory->save();
            }

            $this->saveImage($request, $post);
            $this->saveMetaImage($request, $post);

            // commit database
            DB::commit();

            // update search index
            // UpdatePostDocument::dispatch($locale, $post->id);

            $slider = $request->input('is_slider');
            if ($slider) {
                CreateSliderFromPost::dispatch($locale,$post);
            }

            // dispatch cache jobs
            $type = $request->input('type');
            CachePostColumnOneResponse::dispatch($locale, 'column1');
            CachePostColumnTwoResponse::dispatch($locale, 'column2');
            CachePostColumnThreeResponse::dispatch($locale, 'column3');
            CachePostColumnFourResponse::dispatch($locale, 'column4');  

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
     * @OA\Delete (
     *     path="/{locale}/post/{id}",
     *     summary="Delete post",
     *     description="Delete post",
     *     operationId="postDelete",
     *     tags={"Post"},
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
     * @param Post $post
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($locale, Post $post): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('delete post');

        // begin database transaction
        DB::beginTransaction();
        try {
            // delete post
            $post->delete();

            // commit database
            DB::commit();

            // update search index
            // DeletePostDocument::dispatch($locale, $post->id);

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
     * Get trashed posts
     *
     * @param Request $request
     * @param $locale
     * @return AnonymousResourceCollection
     * @throws AuthorizationException
     */
    public function getTrashed(Request $request, $locale): AnonymousResourceCollection
    {
        App::setLocale($locale);

        $this->authorize('viewAny post');

        $query = $request->query('query');
        $sortBy = $request->query('sortBy');
        $direction = $request->query('direction');
        $per_page = $request->query('per_page', 10);

        $posts = Post::onlyTrashed();
        if ($query) {
            $posts = $posts->whereTranslationLike('title', '%' . $query . '%');
        }

        if ($sortBy) {
            $posts = $posts->orderBy($sortBy, $direction);
        } else {
            $posts = $posts->latest();
        }

        if ($per_page === '-1') {
            $results = $posts->get();
            $posts = new LengthAwarePaginator($results, $results->count(), -1);
        } else {
            $posts = $posts->paginate($per_page);
        }

        return PostResource::collection($posts);
    }

    /**
     * Restore all trashed posts
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreTrashed(Request $request, $locale): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('restore post');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                Post::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            } else {
                Post::onlyTrashed()->restore();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
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
     * Restore single trashed post
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function restoreSingleTrashed($locale, $id): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('restore post');

        // begin database transaction
        DB::beginTransaction();
        try {
            Post::onlyTrashed()
                ->where('id', '=', $id)
                ->restore();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.restore')
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
     * Permanently delete all trashed posts
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceDelete(Request $request, $locale): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('forceDelete post');

        $ids = explode(',', $request->get('ids'));

        // begin database transaction
        DB::beginTransaction();
        try {
            if (isset($ids)) {
                $posts = Post::onlyTrashed()
                    ->whereIn('id', $ids)
                    ->get();
            } else {
                $posts = Post::onlyTrashed()->get();
            }
            foreach ($posts as $post) {
                // delete post
                $post->forceDelete();
            }

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
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
     * Permanently delete single trashed post
     *
     * @param $locale
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function forceSingleDelete($locale, $id): JsonResponse
    {
        App::setLocale($locale);

        $this->authorize('forceDelete post');

        // begin database transaction
        DB::beginTransaction();
        try {
            $post = Post::onlyTrashed()
                ->where('id', '=', $id)
                ->first();

            // delete post
            $post->forceDelete();

            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.delete')
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
     * @OA\Get (
     *     path="/{locale}/post-slug/{title}",
     *     summary="Check unique slug",
     *     description="Check unique slug for model",
     *     operationId="postSlug",
     *     tags={"Post"},
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
            $latest = PostTranslation::query()->where('slug', '=', $slug)
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
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function saveAssets(Request $request, Post $post): JsonResponse
    {
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $file_name = $file->getClientOriginalName();
                $file_extension = $file->getClientOriginalExtension();
                $name = Str::slug(pathinfo($file_name, PATHINFO_FILENAME));
                $extension = Str::lower($file_extension);

                $url = $post->addMedia($file)
                    ->usingName($name)
                    ->usingFileName($name . '.' . $extension)
                    ->toMediaCollection('assets')
                    ->getFullUrl();

                return response()->json([
                    'message' => Lang::get('crud.upload'),
                    'location' => $url,
                    'name' => $name
                ]);
            } else {
                return response()->json([
                    'message' => Lang::get('crud.error'),
                    'location' => null,
                    'name' => null,
                ], 404);
            }
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage(),
                'location' => null,
                'name' => null,
            ], 400);
        }
    }

     /**
     * Get post getStatistic.
     * @param Request $request
      * @return JsonResponse
     */
    public function getStatistic(Request $request):JsonResponse
    {
        $this->authorize('view post');
        $query = $request->query('query');
        // $from_date = $request->query('fromDate');
        // $to_date = $request->query('toDate');
        $total_post = 0;
        $date_range = 'all';

        $total_today_new_post = Post::whereDate('datetime', Carbon::today())->count();

        // if (isset($to_date) && isset($from_date)) {
        //     $total_customer = Customer::whereBetween('created_at', [strval($from_date), strval($to_date)])->count();
        // } else

        $total_post = Post::count();

        // if($from_date && $to_date) {
        //     $from_date = Carbon::parse($from_date)->format('d/m/Y');
        //     $to_date = Carbon::parse($to_date)->format('d/m/Y');

        //    $date_range = "$from_date to $to_date";
        // }

        $last_thirty_days_post = Post::whereDate('datetime', '>', Carbon::now()->subDays(30))->count();
        $last_prev_month_post = Post::whereBetween('datetime', [strval(Carbon::now()->subDays(60)), strval(Carbon::now()->subDays(30))])->count();
        $total_post_creation_progress = $last_thirty_days_post - $last_prev_month_post;

        if($total_post_creation_progress>0) $total_post_creation_progress = "+$total_post_creation_progress";

        return response()->json([
            'today'=> Carbon::now()->isoFormat('D MMM Y'),
            'today_new_post'=> $total_today_new_post?:0,
            'last_thirty_days_new_post'=> $last_thirty_days_post?:0,
            'last_thirty_days_progress'=> "$total_post_creation_progress"?:0,
            'total_post'=> $total_post?:0,
            'date_range_total_post'=> 'all'
        ]);
    }
}