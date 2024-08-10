<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class PostApi
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="PostApi",
 *     description="PostResponse model",
 * )
 */
class PostApi
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Post ID",
     *     title="id",
     *     readOnly=true
     * )
     *
     * @var integer
     */
    private int $id;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post title",
     *     title="title",
     * )
     *
     * @var string
     */
    private string $title;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post slug",
     *     title="slug",
     * )
     *
     * @var string
     */
    private string $slug;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post excerpt",
     *     title="excerpt",
     * )
     *
     * @var string
     */
    private string $excerpt;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="Post image",
     *     title="image",
     * )
     *
     * @var string
     */
    private string $image;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post image caption",
     *     title="caption",
     * )
     *
     * @var string
     */
    private string $caption;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="Post datetime",
     *     title="datetime",
     * )
     *
     * @var string
     */
    private string $datetime;

    /**
     * @OA\Property(
     *     type="object",
     *     description="Post category",
     *     title="category",
     *     ref="#/components/schemas/CategoryCommonApi"
     * )
     *
     * @var string
     */
    private string $category;
}
