<?php

namespace App\Schema;

use OpenApi\Annotations as OA;

/**
 * Class Category
 *
 * @package App\Models
 *
 * @OA\Schema(
 *     title="Category",
 *     description="Category model",
 *     required={"name", "slug", "status", "ordering"}
 * )
 */
class Category
{
    /**
     * @OA\Property(
     *     format="int64",
     *     type="integer",
     *     description="Category ID",
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
     * )
     * @var bool
     */
    private bool $status;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Category name",
     *     title="name",
     * )
     *
     * @var string
     */
    private string $name;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Category slug",
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
     *     description="Category color",
     *     title="color",
     * )
     *
     * @var string
     */
    private string $color;

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
     *     type="integer",
     *     format="int64",
     *     description="Parent ID",
     *     title="parent_id",
     * )
     * @var int
     */
    private int $parent_id;

    /**
     * @OA\Property(
     *     format="string",
     *     type="string",
     *     description="Category meta title",
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
     *     description="Category meta description",
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
     *     description="Category meta image",
     *     title="meta_image",
     * )
     *
     * @var string
     */
    private string $meta_image;
}
