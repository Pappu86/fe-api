<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class CategoryResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="CategoryResponse",
 *     description="Category list response",
 * )
 */
class CategoryResponse
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Category ID",
     *     title="id",
     * )
     *
     * @var integer
     */
    private int $id;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Category name",
     *     title="name",
     * )
     *
     * @var string
     */
    private string $name;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Category slug",
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
     *     description="Category color",
     *     title="color",
     * )
     *
     * @var string
     */
    private string $color;

    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Category posts count",
     *     title="posts_count",
     * )
     *
     * @var int
     */
    private int $posts_count;

    /**
     * @OA\Property(
     *     description="children",
     *     title="Children",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/CategoryChildResponse"
     * )
     * ),
     *
     * @var array
     */
    private array $children;
}
