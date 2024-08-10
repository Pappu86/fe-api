<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class RoleEditResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="RoleEditResponse",
 *     description="Role edit response",
 * )
 */
class RoleEditResponse extends BaseModel
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="ID",
     *     title="User ID",
     * )
     *
     * @var integer
     */
    private int $id;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="User name",
     *     title="Name",
     * )
     *
     * @var string
     */
    private string $name;

    /**
     * @OA\Property(
     *     description="permissions",
     *     title="Permission",
     *     type="array",
     *     @OA\Items(
     *     type="string",
     *     format="string",
     *     example="view user",
     * )
     * ),
     *
     * @var array
     */
    private array $permissions;
}
