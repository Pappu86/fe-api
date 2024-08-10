<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class FilterResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="FilterResponse",
 *     description="Filter option response",
 * )
 */
class FilterResponse
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Filter value",
     *     title="Value",
     * )
     *
     * @var integer
     */
    private int $value;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Filter text",
     *     title="Text",
     * )
     *
     * @var string
     */
    private string $text;
}
