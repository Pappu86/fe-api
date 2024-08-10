<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PostMoreApiResource;
use App\Jobs\UpdatePostContent;
use App\Jobs\UpdatePostProperty;
use App\Jobs\Search\SearchMissingPostsImport;
use App\Traits\PostResponse;
use App\Traits\CategoryResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\PostTranslation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

class PostApiController extends Controller
{
    use PostResponse, CategoryResponse;

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/post/{type}",
     *     summary="Get posts",
     *     description="Get posts",
     *     operationId="postApiIndex",
     *     tags={"PostApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Post type",
     *     in="path",
     *     name="type",
     *     required=true,
     *     example="column1",
     *     @OA\Schema(
     *       type="string",
     *    )
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
     * @param $locale
     * @param $type
     * @return JsonResponse
     */
    public function index($locale, $type): JsonResponse
    {
        $items = Cache::get('post_' . $type . '_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            $posts = match ($type) {
                'column1' => $this->getColumnOnePosts($locale, $type),
                'column2' => $this->getColumnTwoPosts($locale, $type),
                'column3' => $this->getColumnThreePosts($locale, $type),
                'column4' => $this->getColumnFourPosts($locale, $type),
            };
            return response()->json($posts);
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/post/{slug}",
     *     summary="Get post",
     *     description="Get post",
     *     operationId="postApiShow",
     *     tags={"PostApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Post slug",
     *     in="path",
     *     name="slug",
     *     required=true,
     *     @OA\Schema(
     *       type="string",
     *    )
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
     * @param $locale
     * @param $slug
     * @return JsonResponse
     */
    public function show($locale, $slug): JsonResponse
    {
        $item = Cache::get('post_' . $slug . '_' . $locale);
        if ($item !== null) {
            return response()->json($item);
        } else {
            return response()->json($this->getPost($locale, $slug));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/posts",
     *     summary="Get all posts",
     *     description="Get all posts by paginate",
     *     operationId="postsApi",
     *     tags={"PostApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
    public function allPosts(Request $request, $locale): JsonResponse
    {
        $limit = (int)$request->query('limit', 12);

        $posts = $this->getPosts($locale)
            ->orderByDesc('id')
            ->cursorPaginate($limit);

        return response()->json([
            'items' => PostMoreApiResource::collection($posts->items()),
            'nextPageUrl' => $posts->nextPageUrl(),
            'prevPageUrl' => $posts->previousPageUrl(),
            'perPage' => $posts->perPage(),
            'hasPages' => $posts->hasMorePages()
        ]);
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/post-more/{slug}",
     *     summary="Get post more",
     *     description="Get post more",
     *     operationId="postMoreApiShow",
     *     tags={"PostApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
     *     @OA\Parameter (
     *     ref="#/components/parameters/locale"
     * ),
     *     @OA\Parameter(
     *     description="Post slug",
     *     in="path",
     *     name="slug",
     *     required=true,
     *     @OA\Schema(
     *       type="string",
     *    )
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
     * @param $locale
     * @param $slug
     * @return JsonResponse
     */
    public function postMore($locale, $slug): JsonResponse
    {
        $item = Cache::get('post_more_' . $slug . '_' . $locale);
        if ($item !== null) {
            return response()->json($item);
        } else {
            return response()->json($this->getPostMore($locale, $slug));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/post-most-read",
     *     summary="Get post most read",
     *     description="Get post most read",
     *     operationId="postMostReadApiShow",
     *     tags={"PostApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function postMostRead($locale): JsonResponse
    {               
        $item = Cache::get('post_mostRead_' . $locale);
        if ($item !== null) {
            return response()->json($item);
        } else {
            return response()->json($this->getPostMostRead($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-economy",
     *     summary="Get posts by economy category",
     *     description="Get posts by economy category",
     *     operationId="postApiCategoryIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryEconomyPosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_economy_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getEconomyCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-sports",
     *     summary="Get posts by sports category",
     *     description="Get posts by sports category",
     *     operationId="postApiSportsIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategorySportsPosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_sports_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getSportsCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-national",
     *     summary="Get posts by national category",
     *     description="Get posts by national category",
     *     operationId="postApiNationalIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryNationalPosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_national_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getNationalCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-trade",
     *     summary="Get posts by trade category",
     *     description="Get posts by trade category",
     *     operationId="postApiTradeIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryTradePosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_trade_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getTradeCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-stock",
     *     summary="Get posts by stock category",
     *     description="Get posts by stock category",
     *     operationId="postApiStockIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryStockPosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_stock_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getStockCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/most-read",
     *     summary="Get posts by views count",
     *     description="Get posts by views count",
     *     operationId="postApiMostReadkIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getMostReadPosts($locale): JsonResponse
    {
        // Get last week most read news
        return response()->json($this->getMostReadResponse($locale));
        // $items = Cache::get('post_category_mostRead_' . $locale);
        // if ($items !== null) {
        //     return response()->json($items);
        // } else {
        //     return response()->json($this->getMostReadResponse($locale));
        // }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-world",
     *     summary="Get posts by world category",
     *     description="Get posts by world category",
     *     operationId="postApiWorldIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryWorldPosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_world_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getWorldCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-lifestyle",
     *     summary="Get posts by lifestyle category",
     *     description="Get posts by lifestyle category",
     *     operationId="postApilifestyleIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryLifestylePosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_lifestyle_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getLifestyleCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-education",
     *     summary="Get posts by education category",
     *     description="Get posts by education category",
     *     operationId="postApiEducationIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryEducationPosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_education_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getEducationCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-more",
     *     summary="Get posts by category more",
     *     description="Get posts by category more",
     *     operationId="postApiMoreIndex",
     *     tags={"PostMoreApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryMore($locale): JsonResponse
    {        
        $items = Cache::get('post_category_more_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getCategoryMoreResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-youth-and-entrepreneurship",
     *     summary="Get posts by youth-and-entrepreneurship category",
     *     description="Get posts by youth-and-entrepreneurship category",
     *     operationId="postApiYouthAndEntrepreneurshipIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryYouthPosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_youth_and_entrepreneurship_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getYouthCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-personal-finance",
     *     summary="Get posts by personal-finance category",
     *     description="Get posts by personal-finance category",
     *     operationId="postApiPersonalFinanceIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryPersonalFinancePosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_personal_finance_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getPersonalFinanceCategoryResponse($locale));
        }
    }

    /**
     * @OA\Get (
     *     path="/api/{locale}/home/category-bangla",
     *     summary="Get posts by bangla category",
     *     description="Get posts by bangla category",
     *     operationId="postApiBanglaIndex",
     *     tags={"PostCategoryApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryBanglaPosts($locale): JsonResponse
    {
        $items = Cache::get('post_category_bangla_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getBanglaCategoryResponse($locale));
        }
    }

    /*
    * @param Request $request
    * @return JsonResponse
    */
    function updatePostContent( Request $request){
        $chunkStep = $request->get('chunk');
        // begin database transaction
        DB::beginTransaction();
        try {
            Post::chunk($chunkStep, function ($posts) {         
                $ids = $posts->pluck('id');
                UpdatePostContent::dispatch($ids);
            });
            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
            ], 200);
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

    /*
    * @param Request $request
    * @return JsonResponse
    */
    function updatePostProperty( Request $request){
        $chunkStep = $request->get('chunk');
        // begin database transaction
        DB::beginTransaction();
        try {
            PostTranslation::query()->whereNull('title')->chunk($chunkStep, function ($posts) {         
                $ids = $posts->pluck('id');                
                UpdatePostProperty::dispatch($ids);
            });
            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
            ], 200);
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

    /*
    * @param Request $request
    * @return JsonResponse
    */
    function searchMissingPostsImport( Request $request){
        $chunkStep = $request->get('chunk');
        // begin database transaction
        DB::beginTransaction();
        try {
            $locale = 'en';
            $postIds=$request->get('postIds');            
            if(isset($postIds)){
                log::info("all-postIds", [$postIds]);
                foreach ($postIds as $postId) {
                 SearchMissingPostsImport::dispatch($locale, $postId);
                }
            }          
            
            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
            ], 200);
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
     *     path="/api/{locale}/home/category-views",
     *     summary="Get posts by category views",
     *     description="Get posts by category views",
     *     operationId="postApiViewsIndex",
     *     tags={"PostViewsApi"},
     *     @OA\Server(
     *     url="/",
     *     description="API base url"
     * ),
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
     * @param $locale
     * @return JsonResponse
     */
    public function getCategoryViewsPosts($locale): JsonResponse
    {        
        $items = Cache::get('post_category_views_' . $locale);
        if ($items !== null) {
            return response()->json($items);
        } else {
            return response()->json($this->getCategoryViewsResponse($locale));
        }
    }

    /*
    * @param Request $request
    * @return JsonResponse
    */
    function removeSearchIndexFromMailisearch( Request $request){
        $chunkStep = $request->get('chunk');
        // begin database transaction
        DB::beginTransaction();
        try {
            $posts = Post::query()->latest()->limit(600)->get();
            $ids = $posts->pluck('id');
            // Log::info("all posts", [$ids]);
            if($ids){
                UpdatePostContent::dispatch($locale,$ids);
            }
            // Post::chunk($chunkStep, function ($posts) {         
            //     $ids = $posts->pluck('id');
            //     UpdatePostContent::dispatch($ids);
            // });
            
            // commit database
            DB::commit();
            // return success message
            return response()->json([
                'message' => Lang::get('crud.update'),
            ], 200);
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
}