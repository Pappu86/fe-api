<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class UserLogResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="UserLogResponse",
 *     description="User log response",
 * )
 */
class UserLogResponse extends BaseModel
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="User ID",
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
     *     description="User name",
     *     title="name",
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
     *     title="email",
     * )
     *
     * @var string
     */
    private string $email;

    /**
     * @OA\Property(
     *     format="date",
     *     type="string",
     *     description="Log date",
     *     title="date",
     * )
     *
     * @var string
     */
    private string $date;

    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="User id",
     *     title="user_id",
     * )
     *
     * @var int
     */
    private int $user_id;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Log status",
     *     title="login_status",
     * )
     *
     * @var string
     */
    private string $login_status;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="Log last login",
     *     title="last_login",
     * )
     *
     * @var string
     */
    private string $last_login;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="Log last logout",
     *     title="last_logout",
     * )
     *
     * @var string
     */
    private string $last_logout;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="User browser",
     *     title="browser",
     * )
     *
     * @var string
     */
    private string $browser;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="User operating system",
     *     title="os",
     * )
     *
     * @var string
     */
    private string $os;
}
