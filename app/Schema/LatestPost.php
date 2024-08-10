<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class LatestPost
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="LatestPost",
 *     description="Latest Post model",
 *     required={
 *     "title", "status",
 *     }
 * )
 */
class LatestPost
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Latest Post ID",
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
     *     description="Latest Post title",
     *     title="title",
     * )
     *
     * @var string
     */
    private string $title;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Latest Post url",
     *     title="url",
     * )
     *
     * @var string
     */
    private string $url;
}
