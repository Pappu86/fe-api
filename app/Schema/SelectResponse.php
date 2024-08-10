<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class SelectResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="SelectResponse",
 *     description="Select box response",
 * )
 */
class SelectResponse
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
}
