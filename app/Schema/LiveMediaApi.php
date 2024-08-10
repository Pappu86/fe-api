<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class LiveMediaApi
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="LiveMediaApi",
 *     description="Live media model",
 *     required={
 *     "title", "status", "image", "content", "start_at", "end_at"
 *     }
 * )
 */
class LiveMediaApi
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Live media ID",
     *     title="id",
     *     readOnly=true
     * )
     *
     * @var integer
     */
    private int $id;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Live media title",
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
     *     description="Live media subtitle",
     *     title="title",
     * )
     *
     * @var string
     */
    private string $subtitle;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="Live media image for thumbnail",
     *     title="image",
     * )
     *
     * @var string
     */
    private string $image;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Live media Iframe script",
     *     title="content",
     * )
     *
     * @var string
     */
    private string $content;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="Start datetime",
     *     title="startAt",
     * )
     *
     * @var string
     */
    private string $startAt;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="End datetime",
     *     title="endAt",
     * )
     *
     * @var string
     */
    private string $endAt;
}
