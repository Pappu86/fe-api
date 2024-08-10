<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class Role
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="Role",
 *     description="Role model",
 * )
 */
class Role extends BaseModel
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
     *     type="integer",
     *     description="Users count",
     *     title="UsersCount",
     * )
     *
     * @var int
     */
    private int $users_count;
}
