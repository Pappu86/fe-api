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
class CategoryTreeResponse
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
     *     description="children",
     *     title="Children",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/SelectResponse"
     * )
     * ),
     *
     * @var array
     */
    private array $children;
}
