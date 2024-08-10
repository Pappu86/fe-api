<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class CategoryChildResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="CategoryChildResponse",
 *     description="Category children response",
 * )
 */
class CategoryChildResponse
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
}
