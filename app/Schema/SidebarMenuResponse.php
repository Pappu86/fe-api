<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class SidebarMenuResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="SidebarMenuResponse",
 *     description="Sidebar menu list response",
 * )
 */
class SidebarMenuResponse
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
     *     description="children",
     *     title="Children",
     *     type="array",
     *     @OA\Items(
     *     ref="#/components/schemas/SidebarChildMenuResponse"
     * )
     * ),
     *
     * @var array
     */
    private array $children;
}
