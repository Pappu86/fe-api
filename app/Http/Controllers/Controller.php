<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   title="The Financial Express API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     name="Eliyas Hossain",
 *     email="eliyas.batterylowinteractive@gmail.com",
 *     url="https://batterylowinteractive.com"
 *   )
 * ),
 * @OA\Server(
 *     url="/admin",
 *     description="Admin API base url"
 * ),
 * @OA\Parameter(
 *     description="App locale",
 *     in="path",
 *     name="locale",
 *     required=true,
 *     example="en",
 *     @OA\Schema(
 *     type="string",
 * )
 * ),
 * @OA\Parameter(
 *    description="Model ID",
 *    in="path",
 *    name="id",
 *    required=true,
 *    example="1",
 *    @OA\Schema(
 *       type="integer",
 *       format="int64"
 *    )
 * ),
 * @OA\Parameter(
 *     description="Search term",
 *     in="query",
 *     name="query",
 *     @OA\Schema(
 *     type="string",
 * )
 * ),
 * @OA\Parameter(
 *     description="Page number",
 *     in="query",
 *     name="page",
 *     example="1",
 *     @OA\Schema(
 *     type="integer",
 *     format="int64"
 * )
 * ),
 * @OA\Parameter(
 *     description="Sort by",
 *     in="query",
 *     name="sortBy",
 *     @OA\Schema(
 *     type="string",
 * )
 * ),
 * @OA\Parameter(
 *     description="Sort direction",
 *     in="query",
 *     name="direction",
 *     example="asc",
 *     @OA\Schema(
 *     type="string",
 *     enum={"asc", "desc"}
 * )
 * ),
 * @OA\Parameter(
 *     description="Items per page",
 *     in="query",
 *     name="per_page",
 *     example="10",
 *     @OA\Schema(
 *     type="integer",
 *     format="int64"
 * )
 * ),
 * @OA\Response(
 *     response=200,
 *     description="Success",
 *     @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Success"),
 *  ),
 *  ),
 * @OA\Response(
 *     response=400,
 *     description="Bad Request",
 *     @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Something weng wrong, please try again later."),
 *  )
 *  ),
 * @OA\Response(
 *     response=401,
 *     description="Unauthenticated",
 *     @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Please sign in to use this service.")
 *  )
 *  ),
 * @OA\Response(
 *     response=403,
 *     description="Forbidden",
 *     @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="This action is unauthorized.")
 *  )
 *  ),
 * @OA\Response(
 *     response=404,
 *     description="Not Found",
 *     @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Resource Not Found."),
 *  )
 *  ),
 * @OA\Response(
 *     response=500,
 *     description="Internal Server Error",
 *     @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Internal Server Error."),
 *  )
 *  ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
