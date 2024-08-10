<?php

namespace App\Http\Resources\Api;

use App\Traits\PostSlug;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCommonApiResource extends JsonResource
{
    use PostSlug;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => '/' . $this->slug,
            'color' => $this->color,
        ];
    }
}
