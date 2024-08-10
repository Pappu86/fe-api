<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryMoreApiResource extends JsonResource
{
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
            'name' => $this->parent?->name . ' / ' . $this->name,
            'slug' => "/$this->slug",
            'color' => $this->color,
            'featured' => $this->featured,
            'posts' => $this->posts,
        ];
    }
}
