<?php

namespace App\Http\Resources\Api\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryStockApiResource extends JsonResource
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
            'name' => $this->name,
            'slug' => "/$this->slug",
            'color' => $this->color,
            'featured' => $this->featured,
            'posts' => $this->posts,
            'bangladesh' => $this->bangladesh,
            'global' => $this->global,
            'oped' => $this->oped,
        ];
    }
}
