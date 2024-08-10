<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class SidebarChildMenuResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="SidebarChildMenuResponse",
 *     description="Sidebar child menu list response",
 * )
 */
class SidebarChildMenuResponse
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
}
