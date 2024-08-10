<?php

namespace App\Http\Resources\Api\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\CategoryMorePostApiResource;

class CategoryWorldApiResource extends JsonResource
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
            'posts' => CategoryMorePostApiResource::collection($this->posts),
            'asia' => $this->asia,
            'america' => $this->america,
            'europe' => $this->europe,
            'africa' => $this->africa,
        ];
    }
}
