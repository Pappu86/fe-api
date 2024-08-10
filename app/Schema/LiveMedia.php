<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class LiveMedia
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="LiveMedia",
 *     description="Live media model",
 *     required={
 *     "title", "status", "image", "content", "start_at", "end_at"
 *     }
 * )
 */
class LiveMedia
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
     *     type="boolean",
     *     description="Check, if resource active or inactive",
     *     title="status",
     * )
     * @var bool
     */
    private bool $status;

    /**
     * @OA\Property(
     *     type="boolean",
     *     description="Featured model",
     *     title="featured",
     * )
     * @var bool
     */
    private bool $featured;

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
     *     title="start_at",
     * )
     *
     * @var string
     */
    private string $start_at;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="End datetime",
     *     title="end_at",
     * )
     *
     * @var string
     */
    private string $end_at;
}
