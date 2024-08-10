<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class Advertisement
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="Advertisement",
 *     description="Advertisement model",
 *     required={
 *     "title", "status", "type", "start_date", "end_date", "provider_id", "position_id"
 *     }
 * )
 */
class Advertisement
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Advertisement ID",
     *     title="id",
     *     readOnly=true
     * )
     *
     * @var integer
     */
    private int $id;

    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Ads provider ID",
     *     title="provider_id",
     * )
     *
     * @var integer
     */
    private int $provider_id;

    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Ads position ID",
     *     title="position_id",
     * )
     *
     * @var integer
     */
    private int $position_id;

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
     *     description="Advertisement title",
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
     *     example="image",
     *     description="Advertisement type",
     *     title="type",
     *     enum={"image", "video", "iframe", "document"}
     * )
     *
     * @var string
     */
    private string $type;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Advertisement Iframe script",
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
     *     description="Advertisement image",
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
     *     description="Advertisement video",
     *     title="video",
     * )
     *
     * @var string
     */
    private string $video;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="Advertisement document",
     *     title="document",
     * )
     *
     * @var string
     */
    private string $document;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Advertisement link",
     *     title="link",
     * )
     *
     * @var string
     */
    private string $link;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Advertisement mobile Iframe script",
     *     title="mobile_content",
     * )
     *
     * @var string
     */
    private string $mobile_content;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="Advertisement mobile image",
     *     title="mobile_image",
     * )
     *
     * @var string
     */
    private string $mobile_image;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="Advertisement mobile video",
     *     title="mobile_video",
     * )
     *
     * @var string
     */
    private string $mobile_video;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="Advertisement mobile document",
     *     title="mobile_document",
     * )
     *
     * @var string
     */
    private string $mobile_document;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Advertisement mobile link",
     *     title="mobile_link",
     * )
     *
     * @var string
     */
    private string $mobile_link;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="Start datetime",
     *     title="start_date",
     * )
     *
     * @var string
     */
    private string $start_date;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="End datetime",
     *     title="end_date",
     * )
     *
     * @var string
     */
    private string $end_date;

    /**
     * @OA\Property(
     *     type="boolean",
     *     description="External link",
     *     title="is_external",
     * )
     * @var bool
     */
    private bool $is_external;

    /**
     * @OA\Property(
     *     type="boolean",
     *     description="Popup ads",
     *     title="is_modal",
     * )
     * @var bool
     */
    private bool $is_modal;

    /**
     * @OA\Property(
     *     type="boolean",
     *     description="Auto popup ads",
     *     title="is_auto_modal",
     * )
     * @var bool
     */
    private bool $is_auto_modal;

    /**
     * @OA\Property(
     *     type="int",
     *     description="Auto popup duration",
     *     title="auto_modal_duration",
     * )
     * @var int
     */
    private int $auto_modal_duration;
}
