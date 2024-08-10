<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class Type
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="Type",
 *     description="Type model",
 *     required={"key", "label"}
 * )
 */
class Type
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Category ID",
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
     *     description="Type key",
     *     title="key",
     * )
     *
     * @var string
     */
    private string $key;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Type label",
     *     title="label",
     * )
     *
     * @var string
     */
    private string $label;
}
