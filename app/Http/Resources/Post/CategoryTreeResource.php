<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class CategoryTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'children' => CategoryTreeChildResource::collection($this->whenLoaded('children'))
        ];
    }
}
