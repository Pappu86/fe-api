<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class SliderResponse
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="SliderResponse",
 *     description="SliderResponse model",
 *     required={
 *     "title", "status",
 *     }
 * )
 */
class Slider
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="SliderResponse ID",
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
     *     format="int64",
     *     description="Check, if resource active or inactive",
     *     title="status",
     *     example="1"
     * )
     * @var bool
     */
    private bool $status;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     example="iframe",
     *     description="SliderResponse type",
     *     title="type",
     *     enum={"image", "video", "iframe"}
     * )
     *
     * @var string
     */
    private string $type;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="SliderResponse title",
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
     *     description="SliderResponse iframe content",
     *     title="content",
     * )
     *
     * @var string
     */
    private string $content;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="SliderResponse image",
     *     title="image",
     * )
     *
     * @var string
     */
    private string $image;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="SliderResponse video",
     *     title="video",
     * )
     *
     * @var string
     */
    private string $video;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="SliderResponse link",
     *     title="link",
     * )
     *
     * @var string
     */
    private string $link;

    /**
     * @OA\Property(
     *     type="integer",
     *     format="int64",
     *     description="Order number",
     *     title="ordering",
     * )
     * @var int
     */
    private int $ordering;

    /**
     * @OA\Property(
     *     type="boolean",
     *     format="int64",
     *     description="External link",
     *     title="is_external",
     * )
     * @var bool
     */
    private bool $is_external;
}
