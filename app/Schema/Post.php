<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class Post
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="Post",
 *     description="Post model",
 *     required={
 *     "title", "short_title", "slug", "type", "status",
 *     "image", "excerpt", "content", "datetime",
 *     }
 * )
 */
class Post
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Post ID",
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
     *     example=true
     * )
     * @var bool
     */
    private bool $status;

    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Post category ID",
     *     title="category_id",
     * )
     *
     * @var string
     */
    private string $category_id;

    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Post reporter ID",
     *     title="reporter_id",
     * )
     *
     * @var string
     */
    private string $reporter_id;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post type",
     *     title="type",
     * )
     *
     * @var string
     */
    private string $type;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post title",
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
     *     description="Post short_title",
     *     title="short_title",
     * )
     *
     * @var string
     */
    private string $short_title;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post slug",
     *     title="slug",
     * )
     *
     * @var string
     */
    private string $slug;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post shoulder",
     *     title="shoulder",
     * )
     *
     * @var string
     */
    private string $shoulder;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post hanger",
     *     title="hanger",
     * )
     *
     * @var string
     */
    private string $hanger;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post excerpt",
     *     title="excerpt",
     * )
     *
     * @var string
     */
    private string $excerpt;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post content",
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
     *     description="Post image",
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
     *     description="Post image caption",
     *     title="caption",
     * )
     *
     * @var string
     */
    private string $caption;

    /**
     * @OA\Property(
     *     type="boolean",
     *     format="int64",
     *     description="Facebook instant article",
     *     title="is_fb_article",
     * )
     * @var bool
     */
    private bool $is_fb_article;

    /**
     * @OA\Property(
     *     format="date-time",
     *     type="string",
     *     description="Post datetime",
     *     title="datetime",
     * )
     *
     * @var string
     */
    private string $datetime;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post meta title",
     *     title="meta_title",
     * )
     *
     * @var string
     */
    private string $meta_title;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Post meta description",
     *     title="meta_description",
     * )
     *
     * @var string
     */
    private string $meta_description;

    /**
     * @OA\Property(
     *     format="binary",
     *     type="string",
     *     description="Post meta image",
     *     title="meta_image",
     * )
     *
     * @var string
     */
    private string $meta_image;
}
