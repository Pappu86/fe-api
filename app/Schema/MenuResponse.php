<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class MenuResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="MenuResponse",
 *     description="Menu list response",
 * )
 */
class MenuResponse extends BaseModel
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
     *     description="Menu title",
     *     title="Title",
     * )
     *
     * @var string
     */
    private string $title;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Menu link",
     *     title="Link",
     * )
     *
     * @var string
     */
    private string $link;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Menu icon",
     *     title="Icon",
     * )
     *
     * @var string
     */
    private string $icon;

    /**
     * @OA\Property(
     *     description="roles",
     *     title="Roles",
     *     type="array",
     *     @OA\Items(
     *     type="integer",
     *     format="int64",
     *     example="1",
     * )
     * ),
     *
     * @var array
     */
    private array $roles;

    /**
     * @OA\Property(
     *     description="children",
     *     title="Children",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/MenuResponse"
     * )
     * ),
     *
     * @var array
     */
    private array $children;
}
