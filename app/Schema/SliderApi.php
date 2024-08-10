<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class SliderApi
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="SliderApi",
 *     description="SliderResponse model",
 * )
 */
class SliderApi
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
     *     format="string",
     *     type="string",
     *     example="image",
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
     *     description="SliderResponse image, video link or iframe script",
     *     title="content",
     * )
     *
     * @var string
     */
    private string $content;

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
     *     type="boolean",
     *     description="External link",
     *     title="isExternal",
     * )
     * @var bool
     */
    private bool $isExternal;
}
