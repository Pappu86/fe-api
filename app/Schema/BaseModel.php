<?php

namespace App\Schema;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * Class BaseModel
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="BaseModel",
 *     description="Base model",
 * )
 */

abstract class BaseModel extends Model
{
    /**
     * @OA\Property(
     *     type="string",
     *     format="date-time",
     *     description="Initial creation timestamp",
     *     title="created_at",
     *     readOnly="true"
     * )
     * @var string
     */
    private string $created_at;

    /**
     * @OA\Property(
     *     type="string",
     *     format="date-time",
     *     description="Last update timestamp",
     *     title="updated_at",
     *     readOnly="true"
     * )
     * @var string
     */
    private string $updated_at;

    /**
     * @OA\Property(
     *     type="string",
     *     format="date-time",
     *     description="Soft delete timestamp",
     *     title="deleted_at",
     *     readOnly="true"
     * )
     * @var string
     */
    private string $deleted_at;
}
