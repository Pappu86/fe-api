<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class User
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 * )
 */
class User extends BaseModel
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
     *     format="email",
     *     type="string",
     *     description="User email",
     *     title="Email",
     * )
     *
     * @var string
     */
    private string $email;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="User mobile",
     *     title="Mobile",
     * )
     *
     * @var string
     */
    private string $mobile;

    /**
     * @OA\Property(
     *     type="boolean",
     *     description="User status",
     *     title="Status",
     * )
     *
     * @var bool
     */
    private bool $status;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="User avatar",
     *     title="Avatar",
     * )
     *
     * @var string
     */
    private string $avatar;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="User role",
     *     title="Role",
     * )
     *
     * @var string
     */
    private string $role;
}
