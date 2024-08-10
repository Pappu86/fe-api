<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use MeiliSearch\Client as MeiliSearch;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Api\PostSearchApiResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Throwable;

class PostSearchApiController extends Controller
{
    /**
     * @var MeiliSearch
     */
    private MeiliSearch $meilisearch;

    public function __construct()
    {
        $this->meilisearch = new MeiliSearch(config('meilisearch.host'), config('meilisearch.key'));
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/post-search",
     *     summary="Search post",
     *     description="Get posts by search",
     *     operationId="searchPostApi",
     *     tags={"SearchApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     *     ref="#/components/parameters/per_page"
     * ),
     *     @OA\Parameter(
     *     description="Filter by reporter",
     *     in="query",
     *     name="reporter",
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     * )
     * ),
     *     @OA\Parameter(
     *     description="Filter by categories (multiple id sepated by comma",
     *     in="query",
     *     name="categories",
     *     @OA\Schema(
     *     type="string"
     * )
     * ),
     *     @OA\Parameter(
     *     description="Filter by start date (unix time)",
     *     in="query",
     *     name="start_date",
     *     @OA\Schema(
     *     type="date-time",
     *     format="int64"
     * )
     * ),
     *     @OA\Parameter(
     *     description="Filter by end date (unix time)",
     *     in="query",
     *     name="end_date",
     *     @OA\Schema(
     *     type="date-time",
     *     format="int64"
     * )
     * ),
     *     @OA\Parameter(
     *     description="Sort by",
     *     in="query",
     *     name="sort_by",
     *     @OA\Schema(
     *     type="string",
     *     enum={"asc", "desc", "relevant"}
     * )
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/PostApi"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     */
    public function search(Request $request, $locale): JsonResponse
    {
        $request->validate([
            'query' => 'required|string',
            'reporter' => 'nullable|numeric',
            'categories' => 'nullable',
            'categories.*' => 'numeric',
            'start_date' => 'nullable|date_format:U',
            'end_date' => 'required_with:start_date|date_format:U',
            'sort_by' => 'nullable|in:relevant,desc,asc'
        ]);
        try {
            // Log::info("meilisearch", [$this->meilisearch]);
            $index = $this->meilisearch->index('posts_' . $locale);
            $query = $request->query('query');
            $perPage = (int)$request->query('per_page', 20);
            $page = (int)$request->query('page', 1);
            // filters
            $reporter = (int)$request->query('reporter');
            $categoryQuery=$request->query('categories');
            $categories = json_decode('[' . $request->query('categories') . ']', true);
            $startDate = (int)$request->query('start_date');
            $endDate = (int)$request->query('end_date');
            // sort
            $sortBy = $request->query('sort_by');

            // build filter array
            $filters = collect();
            if ($request->filled('reporter')) {
                $filters->push('reporterId = ' . $reporter);
            }
            if ($request->filled('categories')) {
                $array = collect();
                collect($categories)->each(function ($item) use ($array) {
                    $array->push('categoryId = ' . $item);
                });
                $filters->push($array->toArray());
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $filters->push('publishedAt >= ' . $startDate, 'publishedAt <= ' . $endDate);
            }
            // build sort array
            $sort = null;
            if ($request->filled('sort_by')) {
                if ($sortBy === 'asc' || $sortBy === 'desc') {
                    $sort = ['publishedAt:' . $sortBy];
                }
            }

            $limit = $perPage;
            $offset = ($page - 1) * $perPage;

            // Log::info("index", [$index]);
            // Log::info("Test", [$query]);

            $result = $index->search($query, [
                'offset' => $offset,
                'limit' => $limit,
                'filter' => $filters->toArray(),
                'sort' => $sort,
            ]);

            $total = $result->getNbHits();
            $hasMorePages = ($perPage * $page) < $total;
            if ($hasMorePages) {
                $page++;
            }
            $nextPageUrl = "api/{$locale}/post-search?page={$page}&per_page={$perPage}&query={$query}&sort_by={$sortBy}";
            
            if($startDate && $endDate){
                $nextPageUrl =$nextPageUrl."&start_date={$startDate}&end_date={$endDate}"; 
            }
             
             if($categoryQuery){
                 $nextPageUrl =$nextPageUrl."&categories={$categoryQuery}";
             }
             
            return response()->json([
                'items' => $result->getHits(),
                'total' => $total,
                'nextPageUrl' => $nextPageUrl,
                'hasPages' => $hasMorePages,
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/post-search-result",
     *     summary="Search post",
     *     description="Get posts by search key word",
     *     operationId="searchPostApi",
     *     tags={"SearchApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     *     ref="#/components/parameters/per_page"
     * ),
     *     @OA\Parameter(
     *     description="Filter by reporter",
     *     in="query",
     *     name="reporter",
     *     @OA\Schema(
     *     type="integer",
     *     format="int64"
     * )
     * ),
     *     @OA\Parameter(
     *     description="Filter by categories (multiple id sepated by comma",
     *     in="query",
     *     name="categories",
     *     @OA\Schema(
     *     type="string"
     * )
     * ),
     *     @OA\Parameter(
     *     description="Filter by start date (unix time)",
     *     in="query",
     *     name="start_date",
     *     @OA\Schema(
     *     type="date-time",
     *     format="int64"
     * )
     * ),
     *     @OA\Parameter(
     *     description="Filter by end date (unix time)",
     *     in="query",
     *     name="end_date",
     *     @OA\Schema(
     *     type="date-time",
     *     format="int64"
     * )
     * ),
     *     @OA\Parameter(
     *     description="Sort by",
     *     in="query",
     *     name="sort_by",
     *     @OA\Schema(
     *     type="string",
     *     enum={"asc", "desc", "relevant"}
     * )
     * ),
     *     @OA\Response (
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent (
     *     @OA\Property (
     *     property="data",
     *     type="array",
     *     @OA\Items (
     *     ref="#/components/schemas/PostApi"
     * )
     * )
     *  ),
     *  ),
     *     @OA\Response (
     *     response="400",
     *     ref="#/components/responses/400"
     * ),
     *     @OA\Response (
     *     response="5XX",
     *     ref="#/components/responses/500"
     * ),
     * )
     *
     * @param Request $request
     * @param $locale
     * @return JsonResponse
     */
    public function getSearchResult(Request $request, $locale): JsonResponse
    {
        $request->validate([
            'query' => 'required|string',
            'reporter' => 'nullable|numeric',
            'categories' => 'nullable',
            'categories.*' => 'numeric',
            'start_date' => 'nullable|string',
            'end_date' => 'required_with:start_date|string',
            'sort_by' => 'nullable|in:relevant,desc,asc'
        ]);
        try {
            $query = $request->query('query');
            $perPage = (int)$request->query('per_page', 20);
            $page = (int)$request->query('page', 1);
            $reporter = (int)$request->query('reporter');
            $categoryQuery=$request->query('categories');
            $categories = json_decode('[' . $categoryQuery . ']', true);
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $sortBy = $request->query('sort_by');
            $limit = $perPage;

            $posts = DB::table('posts as p')
                ->join('post_translations as pt', function ($join) use ($locale) {
                    $join->on('p.id', '=', 'pt.post_id');
                    $join->where('pt.locale','=', $locale);
                })
                ->join('category_translations as ct', function ($join) use ($locale) {
                    $join->on('ct.category_id', '=', 'p.category_id');
                    $join->where('ct.locale','=', $locale);
                })
                ->join('reporters as r', function ($join) {
                    $join->on('r.id', '=', 'p.reporter_id');
                })
                ->join('reporter_translations as rt', function ($join) use ($locale) {
                    $join->on('rt.reporter_id', '=', 'p.reporter_id');
                    $join->where('rt.locale','=', $locale);
                });
            
            if($reporter){
                $posts=$posts->where('p.reporter_id', $reporter);
            }
            
            if(count($categories)>0){
                $posts=$posts->whereIn('p.category_id', $categories);
            }

            if($startDate && $endDate){
               $start_date = Carbon::parse($startDate)->startOfDay();
               $end_date = Carbon::parse($endDate)->endOfDay();               
               $posts=$posts->whereBetween('p.datetime', [date($start_date), date($end_date)]);
            }
            
            if(isset($query)){
                $posts=$posts->where(function ($query) use ($request) {
                    return $query->where('pt.short_title', "LIKE", "%" . $request->query('query') . "%")
                        ->orWhere('pt.excerpt', "LIKE", "%" . $request->query('query') . "%");
                        // ->orWhere('pt.content', "LIKE", "%" . $request->query('query') . "%");
                });                                        
            }            

            $posts=$posts->select('p.id', 'p.category_id', 'p.reporter_id', 'p.image', 'p.datetime', 'p.created_at', 'pt.title', 'pt.short_title',
                     'pt.slug', 'pt.excerpt', 'pt.content', 'ct.name as category_name', 'ct.slug as category_slug', 'rt.name as reporter_name', 
                     'r.username as reporter_username');

            $total = $posts->count();
            $hasMorePages = ($perPage * $page) < $total;
            if ($hasMorePages) {
                $page++;
            }

            if(isset($sortBy)){
                $posts=$posts->orderBy('id', $sortBy);
            }

            $posts=$posts->paginate($limit );                
            $nextPageUrl = "api/{$locale}/post-search-result?page={$page}&per_page={$perPage}&query={$query}&sort_by={$sortBy}"; 
            
            if($startDate && $endDate){
               $nextPageUrl =$nextPageUrl."&start_date={$startDate}&end_date={$endDate}"; 
            }
            
            if($categoryQuery){
                $nextPageUrl =$nextPageUrl."&categories={$categoryQuery}";
            }
            return response()->json([
                'items' => PostSearchApiResource::collection($posts->items()),
                'total' => $total,
                'nextPageUrl' => $nextPageUrl,
                'hasPages' => $hasMorePages
                ]); 

        } catch (Throwable $exception) {
            return response()->json([
                'message' => Lang::get('crud.error'),
                'error' => $exception->getMessage()
            ], 400);
        }
    }    
}
