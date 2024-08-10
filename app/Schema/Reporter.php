<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class Reporter
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="Reporter",
 *     description="Reporter model",
 *     required={"name", "username", "status"}
 * )
 */
class Reporter
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Repoter ID",
     *     title="id",
     *     readOnly=true
     * )
     *
     * @var integer
     */
    private int $id;

    /**
     * @OA\Property(
     *     type="boolean",
     *     example=true,
     *     description="Check, if resource active or inactive",
     *     title="status",
     * )
     * @var bool
     */
    private bool $status;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Repoter name",
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
     *     description="Reporter username",
     *     title="username",
     * )
     *
     * @var string
     */
    private string $username;

    /**
     * @OA\Property(
     *     format="email",
     *     type="string",
     *     description="Repoter email",
     *     title="email",
     * )
     *
     * @var string
     */
    private string $email;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Repoter mobile",
     *     title="mobile",
     * )
     *
     * @var string
     */
    private string $mobile;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="Repoter avatar",
     *     title="avatar",
     * )
     *
     * @var string
     */
    private string $avatar;
}
